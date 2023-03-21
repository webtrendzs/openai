<?php

declare(strict_types=1);

/*
 * This file is part of [package name].
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace OpenAI\GPT3\Tests;

use OpenAI\GPT3\CloudsasaOpenAIBundle;
use PHPUnit\Framework\TestCase;

class CloudsasaOpenAIBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new CloudsasaOpenAIBundle();

        $this->assertInstanceOf('OpenAI\GPT3\CloudsasaOpenAIBundle', $bundle);
    }
}