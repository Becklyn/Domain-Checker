<?php declare(strict_types=1);

namespace Becklyn\DomainChecker;


class DomainChecker
{
    /**
     * @var null|string
     */
    private $desiredIp;


    /**
     * @param null|string $desiredIp
     */
    public function __construct (?string $desiredIp = null)
    {
        $this->desiredIp = $desiredIp;
    }


    /**
     * Processes the given domain and returns a fully formatted output table row
     *
     * @param string $domain
     * @return array
     */
    public function processDomain (string $domain) : array
    {
        $dnsRecords = dns_get_record($domain, DNS_A);

        if (0 === count($dnsRecords))
        {
            return $this->finalizeFormat([
                "—",
                $domain,
                "—",
                "—",
                "<fg=red>No record found.</>",
            ]);
        }

        if (1 < count($dnsRecords))
        {
            return $this->finalizeFormat([
                "?",
                $domain,
                "?",
                "?",
                "<fg=red>Multiple records found.</>",
            ]);
        }

        $record = $dnsRecords[0];

        $result = [
            $record["type"],
            $domain,
            $record["ip"],
            $record["ttl"],
            "",
        ];

        if (null !== $this->desiredIp)
        {
            $color = ($this->desiredIp === $record["ip"]) ? "green" : "red";

            $result[2] = sprintf(
                "<fg=%s>%s</>",
                $color,
                $result[2]
            );
        }

        return $this->finalizeFormat($result);
    }


    /**
     * Finalizes the format of the row
     *
     * @param array $row
     * @param bool  $forceError
     * @return array
     */
    private function finalizeFormat (array $row, bool $forceError = false) : array
    {
        if (null !== $this->desiredIp)
        {
            if ($forceError || $row[2] !== $this->desiredIp)
            {
                \array_splice($row, 2, 0, ["❌"]);
            }
            else if ($row[2] === $this->desiredIp)
            {
                \array_splice($row, 2, 0, ["✅"]);
            }
        }

        return $row;
    }
}
