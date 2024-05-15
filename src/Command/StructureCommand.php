<?php

namespace WebAPI\Bundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class StructureCommand extends Command
{

    
    protected string $structure;

    public function __construct()
    {
        $this->structure = "\n";
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('webapi:structure');
        $this->setDescription('Generates the Structure class.');
        $this->setHelp('Type webapi:structure [Structure Class Name] to generate the structure class.');
        $this->addArgument('class', InputArgument::REQUIRED, 'Structure Class name.');
    }

    private function translateTypes($type): string {
        switch ($type) {
            case 'integer':
                return 'int';
                break;
            case 'boolean':
                return 'bool';
                break;
            default:
                return $type;
        }
    }

    private function getFields(SymfonyStyle $io, array $fields = [])
    {
        $isEnd = false;

        while (!$isEnd) {
            $isEnd = empty($fieldName = $io->ask("Enter the field name or press `Enter` to finish."));

            if (!$isEnd) {
                $allowedFieldTypes = ['string', 'integer', 'boolean', 'float', 'array', 'object'];
                $translatedFieldTypes = ['string', 'int', 'bool', 'float', 'array', 'object'];

                do {
                    $fieldType = $io->ask("Enter the field type", "string");

                    $fieldType = $this->translateTypes($fieldType);

                    if (!in_array($fieldType, $translatedFieldTypes)) {
                        $io->error("Invalid field type. Please enter one of: " . implode(', ', $allowedFieldTypes));
                    }
                } while (!in_array($fieldType, $translatedFieldTypes));

                $isNullable = $io->ask("The field is nullable", "false");

                if ($isNullable === "true") {
                    $isNullable = true;
                } else if ($isNullable === "false") {
                    $isNullable = false;
                } else {
                    $io->error("Only 'true' or 'false' inputs will be accepted.");
                }

                $fields[] = [
                    "name" => $fieldName,
                    "type" => $fieldType,
                    "null" =>  $isNullable
                ];
            }
        }

        return $fields;
    }

    private function translateNames(string $name): array
    {
        $nameLower = strtolower($name);
        $nameCap = str_replace(' ', '', ucwords(str_replace('_', ' ', $nameLower)));
        return [$nameLower, $nameCap];
    }

    private function generateClass(string $className, array $functions)
    {
        $this->structure .= "class " . $className . "\n";
        $this->structure .= "{ \n";
        foreach ($functions as $function) {
            $this->generateProperties($function['name'], $function['type'], (bool)$function['null']);
        }
        foreach ($functions as $function) {
            $this->generateFunction($function['name'], $function['type'], (bool)$function['null']);
        }
        $this->structure .= "} \n";
    }

    private function generateProperties(string $name, string $type, bool $null)
    {
        $translate = $this->translateNames($name);
        $this->structure .= "\tprotected " . ($null ? '?' : '') . $type . " \$" . $translate[0] . ($null ? ' = null' : '') . ";\n\n";
    }

    private function generateFunction($name, $type, $null)
    {
        $translate = $this->translateNames($name);
        $type_condition = ($null ? '?' : '') . $type;
        $this->structure .= <<<FUNCTION
        
    public function set{$translate[1]}({$type} \${$translate[0]}): void
    {
        \$this->{$translate[0]} = \${$translate[0]};
    }
    
    #[WebAPI\Key(name: '{$translate[0]}')]
    public function get{$translate[1]}(): {$type_condition}
    {
        return \$this->{$translate[0]};
    }

FUNCTION;
    }


    private function generateStructure(string $className, array $functions)
    {
        $this->structure = "<?php \n\n";
        $this->structure .= "namespace App\Structure; \n\n";
        $this->structure .= "use WebAPIBundle\Attribute as WebAPI; \n\n";
        $this->generateClass($className, $functions);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $className = $input->getArgument('class');

        $this->generateStructure($className, $this->getFields($io));

        $directory = "./src/Structure/";
        $filePath = $directory . $className . ".php";

        $filesystem = new Filesystem();

        try {
            $filesystem->dumpFile($filePath, $this->structure);
            $io->success('Success.');
            return Command::SUCCESS;
        } catch (IOExceptionInterface $ioe) {
            $io->error($ioe);
            return Command::FAILURE;
        }
    }
}
