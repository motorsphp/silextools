<?php namespace Motorphp\SilexTools\NetteLibrary\ProviderAdapters;

use Motorphp\SilexTools\Components\ComponentsVisitorAbstract;
use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\NetteLibrary\Method\MethodBody;
use Motorphp\SilexTools\NetteLibrary\SourceCode\Fragment;

class MethodBodyWriter extends ComponentsVisitorAbstract
{
    /** @var array | Factory[]  */
    private $factoriesOther = [];

    private $factoriesFirewall = [];

    private $providers = [];

    function visitFactory(Factory $component)
    {
        if ($component->getPlacement()->isStandalone()) {
            return;
        }
        $firewall = $component->getCapabilities()->getFirewall();

        if ($firewall) {
            $this->factoriesFirewall[] = $component;
        } else {
            $this->factoriesOther[] = $component;
        }
    }

    function visitProvider(Provider $component)
    {
        $this->providers[] = $component;
    }

    /**
     * @param Provider $component
     * @param FragmentWriterProvider $writer
     * @return Fragment
     */
    private function buildProviderFragment(Provider $component, FragmentWriterProvider $writer) : Fragment
    {
        $writer->writeProvider($component);

        /** @var array | Factory[] $firewalls */
        $firewallGroups = $this->groupList(
            function (Factory $factory) {
                return $factory->getCapabilities()->getFirewall();
            },
            $this->firewallsFor($component)
        );

        foreach ($firewallGroups as $group => $factoryList) {
            $writer->startList($group);
            foreach ($factoryList as $factory) {
                $writer->writeEntry($factory);
            }
            $writer->endList();
        }

        foreach ($this->factoriesFor($component) as $factory) {
            $writer->writeEntry($factory);
        }

        return $writer->done();
    }

    private function groupList(\Closure $groupBy, array $list) : array
    {
        $groups = [];
        foreach ($list as $item) {
            $group = $groupBy($item);
            $groups[$group] = [];
        }

        foreach ($list as $item) {
            $group = $groupBy($item);
            $groups[$group][] = $item;
        }

        return $groups;
    }

    private function firewallsFor(Provider $component) : array
    {
        return $this->findFactories($component, $this->factoriesFirewall);
    }

    private function factoriesFor(Provider $component) : array
    {
        return $this->findFactories($component, $this->factoriesOther);
    }

    private function findFactories(Provider $component, array $allFactories) : array
    {
        $key = $component->getId();
        $factories = [];
        foreach ($allFactories as $factory) {
            if ($factory->getPlacement()->atProvider($key)) {
                $factories[] = $factory;
            }
        }

        return $factories;
    }

    /**
     * @return MethodBody
     */
    function getMethodBody() : MethodBody
    {
        $parts = [];
        foreach ($this->providers as $provider) {
            $parts[] = $this->buildProviderFragment($provider, FragmentWriterProvider::newInstance());
        }

        return new MethodBody($parts);
    }

}