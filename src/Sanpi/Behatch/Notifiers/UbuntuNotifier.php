<?php

namespace Sanpi\Behatch\Notifier;

use Behat\Behat\Formatter\ConsoleFormatter;

use Behat\Behat\Event\StepEvent,
    Behat\Behat\Event\SuiteEvent;

class UbuntuNotifier extends ConsoleFormatter
{
    private $lastTimeError = null;

    public static function getDescription()
    {
        return "Warns you in Ubuntu when a scenario is failing";
    }

    protected function getDefaultParameters()
    {
        $behatchDir = str_replace("/features/bootstrap/notifiers", "",__DIR__);

        return array(
            "error_icon" => $behatchDir."/images/gnome-error.png",
            "sad_icon" => $behatchDir."/images/gnome-sad.png",
            "smile_icon" => $behatchDir."/images/gnome-smile.png",
            "spam_timeout" => 180,
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
            $message = 'Scenario : '.$event->getStep()->getParent()->getTitle()."\\";
            $message .= "\n".$event->getStep()->getText()."\\";
            $message .= "\n> ".$event->getException()->getMessage();

            //spam prevention
            if ($this->lastTimeError == null || time() - $this->lastTimeError > $this->parameters->get('spam_timeout')) {
                exec(sprintf("notify-send -i %s -t 1000 'Behat step failure' '%s'", $this->parameters->get('error_icon'),
                    str_replace("'", "`", $message)));
                $this->lastTimeError = time();
            }
        }
    }

    public function afterSuite(SuiteEvent $event)
    {
        if ($event->isCompleted()) {
            $statuses = $event->getLogger()->getScenariosStatuses();
            if ($statuses['failed'] > 0) {
                $message  = "FAILURE";
                $message .= "\n".$statuses['failed']. ' scenario failed';
                $message .= "\n".$statuses['passed']. ' scenario ok';
                exec(sprintf("notify-send -i %s -t 1000 'Behat suite ended' '%s'", $this->parameters->get('sad_icon'), $message));
            }
            else {
                $message  = "SUCCESS";
                $message .= "\n".$statuses['passed']. ' scenario ok';
                exec(sprintf("notify-send -i %s -t 1000 'Behat suite ended' '%s'", $this->parameters->get('smile_icon'), $message));
            }
        }
    }
}
