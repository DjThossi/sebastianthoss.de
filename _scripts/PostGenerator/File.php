<?php
declare(strict_types=1);

class File
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var resource
     */
    private $fileResource;

    /**
     * @var int
     */
    private $charCount = 0;

    /**
     * @var int
     */
    private $maxChars = null;

    /**
     * @param string $fileName
     *
     * @throws Exception
     */
    public function __construct(string $fileName)
    {
        $this->fileResource = fopen($fileName, 'r');
        if ($this->fileResource === false) {
            throw new Exception('File Not Found Exception');
        }
        $this->fileName = $fileName;
    }

    public function __destruct()
    {
        if (is_resource($this->fileResource)) {
            fclose($this->fileResource);
        }
    }

    public function getNextLine(): string
    {
        $line = fgets($this->fileResource);
        if ($line === false) {
            throw new UnexpectedValueException('EOF');
        }

        $this->charCount += strlen($line);

        return trim($line);
    }

    /**
     * @return int
     */
    public function getCharCount(): int
    {
        return $this->charCount;
    }

    public function doesAnotherImageFit(): bool
    {
        if ($this->maxChars === null) {
            $this->maxChars = strlen(file_get_contents($this->fileName));
        }

        return ($this->maxChars > ($this->charCount + 1000));
    }
}