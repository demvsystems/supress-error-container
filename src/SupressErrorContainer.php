<?php

namespace Demv\SupressErrorContainer;

/**
 * Class SupressErrorContainer
 * @package Demv\SupressErrorContainer
 */
final class SupressErrorContainer
{
    /**
     * @var int
     */
    private $supress = 0;
    /**
     * @var string
     */
    private $error;

    /**
     * @param int $error
     */
    public function disable(int $error): void
    {
        $this->supress |= $error;
    }

    /**
     * @param int $error
     */
    public function enable(int $error): void
    {
        $this->supress &= ~$error;
    }

    /**
     * @param int $error
     *
     * @return bool
     */
    public function isSuppressed(int $error): bool
    {
        return ($this->supress & $error) !== 0;
    }

    /**
     * @param int    $errno
     * @param string $error
     *
     * @return bool
     */
    public function handle(int $errno, string $error): bool
    {
        $this->error = $error;

        return $this->isSuppressed($errno);
    }

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function execute(callable $callback)
    {
        $this->error = null;

        try {
            set_error_handler([$this, 'handle']);

            return $callback();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @return bool
     */
    public function wasErrorSupressed(): bool
    {
        return $this->error !== null;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->error;
    }
}
