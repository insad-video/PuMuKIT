<?php

namespace Pumukit\SchemaBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use Pumukit\SchemaBundle\Event\SeriesEvent;
use Pumukit\SchemaBundle\Utils\Mongo\TextIndexUtils;

/**
 * NOTE: This listener is to update the seriesTitle field in each
 *       MultimediaObject for MongoDB Search Index purposes.
 *       Do not modify this listener.
 */
class SeriesListener
{
    private $dm;
    private $mmRepo;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        $this->mmRepo = $dm->getRepository('PumukitSchemaBundle:MultimediaObject');
    }

    public function postUpdate(SeriesEvent $event)
    {
        $series = $event->getSeries();
        $multimediaObjects = $this->mmRepo->findBySeries($series);
        foreach ($multimediaObjects as $multimediaObject) {
            $multimediaObject->setSeries($series);
            $this->dm->persist($multimediaObject);
        }
        $this->updateTextIndex($series);
        $this->dm->flush();
    }

    /**
     * @param $series
     */
    public function updateTextIndex($series)
    {
        $textIndex = array();
        $secondaryTextIndex = array();
        $title = $series->getI18nTitle();
        $text = '';
        $secondaryText = '';
        foreach (array_keys($title) as $lang) {
            if (TextIndexUtils::isSupportedLanguage($lang)) {
                if ($series->getTitle($lang)) {
                    $text = $series->getTitle($lang);
                }
                if ($series->getKeywords($lang)) {
                    $text = $text.' | '.$series->getKeywords($lang);
                }
                if ($series->getDescription($lang)) {
                    $secondaryText = $series->getDescription($lang);
                }
                $textIndex[] = array('indexlanguage' => $lang, 'text' => TextIndexUtils::cleanTextIndex($text));
                $secondaryTextIndex[] = array('indexlanguage' => $lang, 'text' => TextIndexUtils::cleanTextIndex($secondaryText));
            }
        }
        $series->setTextIndex($textIndex);
        $series->setSecondaryTextIndex($secondaryTextIndex);
    }
}
