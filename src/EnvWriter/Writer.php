<?php namespace SevenD\EnvWriter;

class Writer
{
    const LINE_ENDING = PHP_EOL;

    protected $envFilePath;
    protected $env;

    public function __construct($path = null)
    {
        if (is_null($path)) {
            $path = '.env';
        }

        if (file_exists($path)) {
            $this->setEnvFilePath($path);
        }

        $this->parse();
    }

    public function set($key, $value)
    {
        $commentKey = sprintf('#%s', $key);
        $env = $this->getEnv();

        foreach ($env as &$line) {
            if ($key == $line['key'] || $commentKey == $line['key']) {
                $line['value'] = $value;
            }
        }

        return $this->setEnv($env);
    }

    public function get($key)
    {
        $env = $this->getEnv();

        foreach ($env as $line) {
            if ($line['key'] == $key) {
                return $line['value'];
            }
        }
        if (strpos($key, '#') === false) {
            return $this->get(sprintf('#%s', $key));
        }
        return null;
    }

    public function isset($key)
    {
        return (bool) $this->get($key);
    }

    public function unset($key)
    {
        $env = $this->getEnv();
        foreach ($env as $envKey => $line) {
            if ($line['key'] == $key) {
                unset($env[$envKey]);
            }
        }
        $this->setEnv($env);
        return $this;
    }

    public function disable($key)
    {
        $env = $this->getEnv();
        foreach ($env as &$line) {
            if ($line['key'] == $key) {
                $line['key'] = sprintf('#%s', $key);
            }
        }
        $this->setEnv($env);

        return $this;
    }

    public function disabled($key)
    {
        return (bool) $this->get(sprintf('#%s', $key));
    }

    public function enable($key)
    {
        $key = sprintf('#%s', $key);
        $env = $this->getEnv();
        foreach ($env as &$line) {
            if ($line['key'] == $key) {
                $line['key'] = str_replace('#', '', $key);
            }
        }
        $this->setEnv($env);

        return $this;
    }

    public function enabled($key)
    {
        return (bool) !$this->get(sprintf('#%s', $key));
    }

    public function save($outFile = null)
    {
        $this->write($outFile);
    }

    protected function parse()
    {
        $fileLines = [];
        foreach (file($this->getEnvFilePath()) as $line) {
            if (strpos($line, '=')) {
                list($key, $value) = explode('=', trim($line));
                $fileLines[] = [
                    'key' => $key,
                    'value' => $value,
                ];
            } else {
                $fileLines[] = [
                    'key' => '',
                    'value' => trim($line),
                ];
            }
        }
        $this->setEnv($fileLines);
    }

    protected function write($outFile = null)
    {
        if (is_null($outFile)) {
            $outFile = $this->getEnvFilePath();
        }

        $env = $this->getEnv();

        $fileLines = [];
        foreach ($env as $line) {
            $fileLines[] = ($line['key']) ? implode('=', [$line['key'], $line['value']]) : $line['value'];
        }

        file_put_contents($this->getEnvFilePath(), implode(self::LINE_ENDING, $fileLines) . self::LINE_ENDING);

        return $this;
    }

    protected function getEnvFilePath()
    {
        return $this->envFilePath;
    }

    protected function setEnvFilePath($path)
    {
        $this->envFilePath = $path;
        return $this;
    }

    protected function getEnv()
    {
        return $this->env;
    }

    protected function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }
}