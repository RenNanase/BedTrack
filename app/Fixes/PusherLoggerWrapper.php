<?php

namespace App\Fixes;

use Psr\Log\LoggerInterface;

/**
 * A wrapper for Laravel's logger to prevent null values being passed to trim()
 */
class PusherLoggerWrapper implements LoggerInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * System is unusable.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should be logged.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string|\Stringable $message
     * @param array $context
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string|\Stringable $message
     * @param array $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        // Safely handle null or non-string values for level
        if ($level === null || !is_string($level)) {
            $level = 'debug';
        }
        
        // Ensure message is never null
        if ($message === null) {
            $message = '[Empty Message]';
        }
        
        // Convert any non-array context to empty array
        if (!is_array($context)) {
            $context = [];
        }
        
        try {
            $this->logger->log($level, $message, $context);
        } catch (\Throwable $e) {
            // Fallback to error logging if something goes wrong
            error_log('PusherLoggerWrapper error: ' . $e->getMessage());
            
            // Try to log using direct error_log as a last resort
            error_log('Original log message: ' . (is_string($message) ? $message : json_encode($message)));
        }
    }
} 