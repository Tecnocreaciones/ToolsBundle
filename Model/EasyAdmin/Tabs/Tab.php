<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\EasyAdmin\Tabs;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Tab
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Tab {

    const TAB_TITLE = "tab_title";
    const TAB_CONTENT = "tab_content";
    const NAME_CURRENT_TAB = "_st82a";

    private $id;
    private $name;
    private $icon;
    private $options;

    /**
     *
     * @var TabContent
     */
    private $tabsContent;

    public function __construct(array $options = []) {
        $this->tabsContent = [];
//        $this->id = "tab-".uniqid();
        $this->id = null;

        $this->setOptions($options);
    }

    public function setOptions(array $options = []) {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "active_first" => true,
            "entity" => null,
        ]);
        $this->options = $resolver->resolve($options);

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getTabsContent() {
        return $this->tabsContent;
    }

    /**
     * @return TabContent
     */
    public function getLastTabContent() {
        return end($this->tabsContent);
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function getId() {
        if ($this->id === null) {
            $id = "";
            foreach ($this->tabsContent as $tabContent) {
                $id .= $tabContent->getId();
            }
            $this->id = "tab-" . md5($id);
        }
        return $this->id;
    }

    /**
     * Añade una tab
     * @param \Pandco\Bundle\AppBundle\Model\Core\Tab\TabContent $tabContent
     * @return \Pandco\Bundle\AppBundle\Model\Core\Tab\Tab
     * @throws \RuntimeException
     */
    public function addTabContent(TabContent $tabContent) {
        $id = "tc-".md5($tabContent->getTitle());
        if (isset($this->tabsContent[$id])) {
            throw new \RuntimeException(sprintf("The tab content name '%s' is already added.", $tabContent->getName()));
        }
        $this->tabsContent[$id] = $tabContent;
        $tabContent->setId($id);
        return $this;
    }

    public function resolveCurrentTab($current) {
        $activeTab = null;
        if (!empty($current)) {
            $exp = explode("#", $current);
            $id = $this->getId();
            if (count($exp) == 2 && $id === $exp[0]) {
                foreach ($this->tabsContent as $tabContent) {
                    if ($tabContent->getId() === $exp[1]) {
                        $activeTab = $tabContent;
                        break;
                    }
                }
            }
        }
        if ($activeTab === null) {
            $activeTab = reset($this->tabsContent);
        }
        if($this->tabsContent !== null){
            $activeTab->setActive(true);
        }
    }

    /**
     * Convierte la tab a un array
     * @return type
     */
    public function toArray() {
        $data = [
            "id" => $this->getId(),
            "name" => $this->name,
            "tabsContent" => [],
        ];

        foreach ($this->tabsContent as $tabContent) {
            $data["tabsContent"][] = $tabContent->toArray();
        }
        return $data;
    }

    public static function createFromMetadata(array $metadata) {
        $instance = new self();

        if (isset($metadata["title"])) {
            $instance->setName($metadata["title"]);
        }
        if (isset($metadata["icon"])) {
            $instance->setIcon($metadata["icon"]);
        }

        return $instance;
    }

}
