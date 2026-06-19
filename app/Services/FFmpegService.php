<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class FFmpegService
{
    protected string $ffmpegBinary;
    protected string $ffprobeBinary;

    public function __construct()
    {
        $this->ffmpegBinary = config('services.ffmpeg.binary', 'ffmpeg');
        $this->ffprobeBinary = config('services.ffmpeg.ffprobe_binary', 'ffprobe');
    }

    /**
     * Get video duration in seconds.
     */
    public function getDuration(string $videoPath): float
    {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: {$videoPath}");
        }

        $cmd = sprintf(
            '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s',
            escapeshellarg($this->ffprobeBinary),
            escapeshellarg($videoPath)
        );

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || empty($output)) {
            throw new RuntimeException("ffprobe failed to get duration for: {$videoPath}");
        }

        return (float) trim($output[0]);
    }

    /**
     * Extract audio track from video as MP3 file.
     * Output: mono 16kHz 64kbps MP3 (optimal for Whisper, 5 min ≈ 2.4MB).
     */
    public function extractAudio(string $videoPath): string
    {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: {$videoPath}");
        }

        $outputPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'klaar_audio_' . uniqid() . '.mp3';

        $cmd = sprintf(
            '%s -i %s -vn -acodec libmp3lame -ar 16000 -ac 1 -b:a 64k -y %s 2>&1',
            escapeshellarg($this->ffmpegBinary),
            escapeshellarg($videoPath),
            escapeshellarg($outputPath)
        );

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            throw new RuntimeException(
                "FFmpeg audio extraction failed (code {$returnCode}): " . implode("\n", array_slice($output, -5))
            );
        }

        return $outputPath;
    }

    /**
     * Extract keyframes from video at regular intervals.
     *
     * Strategy: adaptive interval based on duration, capped at $maxFrames.
     * Minimum interval 10s to avoid too many similar frames.
     *
     * @return array<string> Absolute paths to extracted JPEG files
     */
    /**
     * @return array<array{path: string, timestamp: int}> Frames with their timestamps
     */
    public function extractKeyframes(string $videoPath, int $maxFrames = 10): array
    {
        if (!file_exists($videoPath)) {
            throw new RuntimeException("Video file not found: {$videoPath}");
        }

        $duration = $this->getDuration($videoPath);

        if ($duration <= 0) {
            return [];
        }

        // Calculate interval: ensure we don't exceed maxFrames, minimum 10s
        $interval = max(10, ceil($duration / $maxFrames));
        $frameCount = (int) floor($duration / $interval);
        $frameCount = max(1, min($frameCount, $maxFrames));

        $outputDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'klaar_frames_' . uniqid();
        if (!mkdir($outputDir, 0755, true) && !is_dir($outputDir)) {
            throw new RuntimeException("Cannot create temp directory: {$outputDir}");
        }

        $frames = [];

        for ($i = 0; $i < $frameCount; $i++) {
            $timestamp = $i * $interval;
            $outputFile = $outputDir . DIRECTORY_SEPARATOR . sprintf('frame_%03d.jpg', $i);

            $cmd = sprintf(
                '%s -ss %d -i %s -vframes 1 -q:v 2 -y %s 2>&1',
                escapeshellarg($this->ffmpegBinary),
                $timestamp,
                escapeshellarg($videoPath),
                escapeshellarg($outputFile)
            );

            $output = [];
            $returnCode = 0;
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFile)) {
                $frames[] = ['path' => $outputFile, 'timestamp' => $timestamp];
            } else {
                Log::warning("FFmpeg keyframe extraction failed at {$timestamp}s", [
                    'video' => $videoPath,
                    'output' => implode("\n", array_slice($output, -3)),
                ]);
            }
        }

        return $frames;
    }
}
