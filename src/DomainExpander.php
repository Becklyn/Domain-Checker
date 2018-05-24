<?php declare(strict_types=1);

namespace Becklyn\DomainChecker;


class DomainExpander
{
    /**
     * Automatically expands all domains, so that for main domains (without sub domain) both the main domain and www. + main domain
     * are added.
     *
     * @param array $list
     * @return array
     */
    public function expandDomainList (array $domains) : array
    {
        $full = [];

        foreach ($domains as $domain)
        {
            $full[$domain] = true;

            // automatically add the "www." variant as well
            if (count(explode(".", $domain)) === 2)
            {
                $full["www.{$domain}"] = true;
            }
        }

        return \array_keys($full);
    }
}
