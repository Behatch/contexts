<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Event\StepEvent;

class DebugContext extends BaseContext
{
    private $screenshotDir;

    public function __construct($screenshotDir = '.')
    {
        $this->screenshotDir = $screenshotDir;
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then /^(?:|I )put a breakpoint$/
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {}
        fwrite(STDOUT, "\033[u");

        return;
    }

    /**
     * Saving a screenshot
     *
     * @When /^I save a screenshot in "(?P<filename>[^"]*)"$/
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->saveScreenshot($filename, $this->screenshotDir);
    }

    /**
     * @AfterStep @javascript
     */
    public function failScreenshots(StepEvent $event)
    {
        if ($event->getResult() == StepEvent::FAILED) {
            $scenarioName = urlencode(str_replace(' ', '_', $event->getStep()->getParent()->getTitle()));
            $filename = sprintf('fail_%s_%s.png', time(), $scenarioName);
            $this->saveScreenshot($filename, $this->screenshotDir);
        }
    }
}
