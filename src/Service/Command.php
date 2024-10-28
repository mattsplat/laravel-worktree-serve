<?php

namespace Mattcoleman\PrServe\Service;

use Closure;
use Laravel\Prompts\Output\ConsoleOutput;
use Symfony\Component\Process\Process;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class Command
{
    protected ConsoleOutput $output;

    public Closure $then;
    public Closure $catch;
    public Process $process;

    public function __construct(
        public array $argv, ?Closure $then = null, ?Closure $catch = null, public bool $stream = false
    )
    {
        $this->output = new ConsoleOutput();
        $this->then = $then ?? function () {
        };
        $this->catch = $catch ?? function () {
        };
        $this->process = new Process($this->argv);
    }

    public function run()
    {
        if ($this->stream) {
            $this->stream();
        } else {
            $this->process->run();
            if (!$this->process->isSuccessful()) {
                error($this->process->getErrorOutput());
                call_user_func($this->catch, $this->process);
            } else {
                info($this->process->getOutput());
                call_user_func($this->then, $this->process);
            }
        }
    }

    public function stream()
    {
        if (!isset($this->process)) {
            $this->process = new Process($this->argv);
        }

        $this->process->start();
        foreach ($this->process as $type => $data) {
            if ($type === Process::ERR) {
                error($data);
            } else {
                info($data);
            }
        }
        if (!$this->process->isSuccessful()) {
            call_user_func($this->catch, $this->process);
        } else {
            call_user_func($this->then, $this->process);
        }
    }

    public static function exec(string $cmd) : ?array
    {
        exec($cmd,$output);
        return $output ?: null;
    }

    public function setWorkingDirectory(string $path)
    {
        $this->process->setWorkingDirectory($path);
    }
}