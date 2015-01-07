<?php

namespace Pumukit\SchemaBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Pumukit\SchemaBundle\Document\Material
 *
 * @MongoDB\EmbeddedDocument
 */
class Material extends Element
{
    /**
     * @var string $name
     *
     * @MongoDB\Raw
     */
    private $name = array('en' => '');

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name, $locale = null)
    {
        if ($locale == null) {
            $locale = $this->locale;
        }
        $this->name[$locale] = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    private function getName($locale = null)
    {
        if ($locale == null) {
            $locale = $this->locale;
        }
        if (!isset($this->name[$locale])) {
            return;
        }
        
        return $this->name[$locale];
    }
    
    /**
     * Set I18n name
     *
     * @param array $name
     */
    public function setI18nName(array $name)
    {
        $this->title = $title;
    }

    /**
     * Get I18n name
     *
     * @return array
     */
    public function getI18nName()
    {
        return $this->name;
    }
}
