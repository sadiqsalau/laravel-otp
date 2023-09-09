<?php

namespace SadiqSalau\LaravelOtp;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class OtpMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Otp class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Otp';


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Otp';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/otp.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the otp already exists'],
        ];
    }
}
