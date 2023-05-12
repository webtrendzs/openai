<?php

declare(strict_types=1);

/*
 * This file is part of contao openai bundle.
 *
 * (c) Ben Mosong
 *
 * @license LGPL-3.0-or-later
 */

namespace OpenAI\ChatGPT\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use OpenAI\ChatGPT\CloudsasaOpenAIBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(CloudsasaOpenAIBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
