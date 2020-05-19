<?php declare(strict_types=1);

namespace Becklyn\DomainChecker;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class CheckCommand extends Command
{
    public static $defaultName = "check";


    /**
     * @var DomainExpander
     */
    private $domainExpander;


    /**
     * @inheritDoc
     */
    public function __construct (?string $name = null)
    {
        parent::__construct($name);
        $this->domainExpander = new DomainExpander();
    }


    /**
     * @inheritdoc
     */
    protected function configure () : void
    {
        $this
            ->setDescription("Checks the DNS for all given domains.")
            ->addArgument("config", InputArgument::OPTIONAL, "The config file where the domains to check are defined. YAML file", "domains.yaml")
            ->addUsage(
                <<<'USAGE'

The config file should be a YAML file in the following format:

```yaml
ip: 127.0.0.1 # (optional)
domains:
    - example.org
    - example.com
```

The list of all domains. If the domains are TLDs, the "www." variant is automatically added.
If an optional IP is given, all domains are checked against this expected ip.
USAGE
            );
    }


    /**
     * @inheritDoc
     */
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument("config");

        $io->title("Domain Checker");

        if (!\is_file($file) || !\is_readable($file))
        {
            $io->error("Config file '{$file}' not found or not readable.");
            return 1;
        }

        try
        {
            $config = Yaml::parseFile($file);
            $domainConfig = (new Processor())->processConfiguration(new YamlStructureConfiguration(), [$config]);
        }
        catch (ParseException $e)
        {
            $io->error("Parsing the config file failed: {$e->getMessage()}");
            return 1;
        }
        catch (InvalidConfigurationException $e)
        {
            $io->error("Invalid configuration given: {$e->getMessage()}");
            return 1;
        }

        $domains = $this->domainExpander->expandDomainList($domainConfig["domains"]);
        $desiredIP = $domainConfig["ip"];
        $domainChecker = new DomainChecker($desiredIP);

        if (null !== $desiredIP)
        {
            $io->comment("Desired IP: <fg=blue>{$desiredIP}</>");
        }

        $headers = [
            "T",        // DNS Record Type
            "Domain",
            "IP",
            "TTL",
            "Comment",
        ];

        if (null !== $desiredIP)
        {
            // append "?" (= Status) column if desired IP is given
            \array_splice($headers, 2, 0, ["?"]);
        }

        $io->table(
            $headers,
            \array_map([$domainChecker, "processDomain"], $domains)
        );

        $io->success("All done.");
        return 0;
    }
}
