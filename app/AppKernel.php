<?php

use MakinaCorpus\Drupal\Sf\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    /**
     * {inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // Reproduce the config_ENV.yml file from Symfony, but keep it
        // optional instead of forcing its usage
        $customConfigFile = $this->rootDir.'/config/config_'.$this->getEnvironment().'.yml';
        if (!file_exists($customConfigFile)) {
            // Else attempt with a default one
            $customConfigFile = $this->rootDir.'/config/config.yml';
        }
        if (!file_exists($customConfigFile)) {
            // If no file is provided by the user, just use the default one
            // that provide sensible defaults for everything to work fine
            $customConfigFile = __DIR__.'/../Resources/config/config.yml';
        }

        $loader->load($customConfigFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }
}
