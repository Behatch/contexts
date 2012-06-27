<?php

namespace Sanpi\Behatch\Notifier;

use Behat\Behat\Formatter\ConsoleFormatter;

use Behat\Behat\Event\StepEvent,
    Behat\Behat\Event\SuiteEvent;

class CampfireNotifier extends ConsoleFormatter
{
    private $lastTimeError = null;

    public static function getDescription()
    {
        return "Warns you in Campfire when a scenario is failing";
    }

    protected function getDefaultParameters()
    {
        return array(
            "campfire_url" => null,
            "campfire_token" => null,
            "campfire_room" => null,
            "campfire_prefix" => null,
            "spam_timeout" => 180
        );
    }

    public static function getSubscribedEvents()
    {
        $events = array('afterStep', 'afterSuite');

        return array_combine($events, $events);
    }

    public function afterStep(StepEvent $event)
    {
        if ($event->getResult() == StepEvent::FAILED) {
            //spam prevention
            if ($this->lastTimeError == null || time() - $this->lastTimeError > $this->parameters->get('spam_timeout')) {
                $message = $this->parameters->get('campfire_prefix')? '['.$this->parameters->get('campfire_prefix').'] ' : '';
                $message .= 'Behat is failing...';
                $message .= "\nScenario : ".$event->getStep()->getParent()->getTitle();
                $message .= "\n  ".$event->getStep()->getText();
                $message .= "\n    ".$event->getException()->getMessage();
                $this->send($message);

                $this->lastTimeError = time();
            }
        }
    }

    public function afterSuite(SuiteEvent $event)
    {
        if ($event->isCompleted()) {
            $prefix = $this->parameters->get('campfire_prefix') ? '['.$this->parameters->get('campfire_prefix').'] ' : '';
            $statuses = $event->getLogger()->getScenariosStatuses();
            if ($statuses['failed'] > 0) {
                $this->send($prefix."Behat suite finished :thumbsdown::shit:");
            }
            else {
                $this->send($prefix."Behat suite finished :thumbsup::sparkles:");
            }
        }
    }

    public function send($message)
    {
        $campfireUrl = $this->parameters->get('campfire_url');
        $campfireToken = $this->parameters->get('campfire_token');
        $campfireRoom = $this->parameters->get('campfire_room');

        if ($campfireUrl == null) {
            throw new Exception("You must set a campfire URL in behat.yml");
        }

        if ($campfireToken == null) {
            throw new Exception("You must set a campfire room in behat.yml");
        }

        if ($campfireRoom == null) {
            throw new Exception("You must set a campfire token in behat.yml");
        }

        $cmd = sprintf("curl -s -u %s:X -H 'Content-Type: application/json' -d %s %s/room/%s/speak.xml",
            $campfireToken,
            escapeshellarg(json_encode(array('message' => array('body' => $message)))),
            trim($campfireUrl, '/'),
            $campfireRoom);
        exec($cmd, $output, $return);
        if ($return != 0) {
            throw new Exception(sprintf("Unable to send campfire notification with curl :\n%s",
                implode("\n", $output)));
        }
    }
}
