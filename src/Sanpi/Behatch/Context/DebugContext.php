<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Event\StepEvent;

class DebugContext extends BaseContext
{
    private $screenshotDir;
    private $screenId;

    public function __construct(array $parameters)
    {
        $behatchDir = str_replace("/features/bootstrap/notifiers", "", __DIR__);
        $this->screenshotDir = isset($parameters["debug"]['screenshot_dir']) ? $parameters["debug"]['screenshot_dir'] : $behatchDir;
        $this->screenId = isset($parameters["debug"]['screen_id']) ? $parameters["debug"]['screen_id'] : ":0";
    }

    /**
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
     * @When /^I save a screenshot in "([^"]*)"$/
     */
    public function iSaveAScreenshotIn($imageFilename)
    {
        sleep(1);
        $this->saveScreenshot($imageFilename);
    }

    /**
     * @AfterStep @javascript
     */
    public function failScreenshots(StepEvent $event)
    {
        if ($event->getResult() == StepEvent::FAILED) {
            $scenarioName = str_replace(" ", "_", $event->getStep()->getParent()->getTitle());
            $this->saveScreenshot(sprintf("fail_%s_%s.png", time(), $scenarioName));
        }
    }

    private function saveScreenshot($filename)
    {
        if ($filename == '') {
            throw new \Exception("You must provide a filename for the screenshot.");
        }

        if (!is_dir($this->screenshotDir)) {
            throw new \Exception(sprintf("The directory %s does not exist.", $this->screenshotDir));
        }

        if (!is_writable($this->screenshotDir)) {
            throw new \Exception(sprintf("The directory %s is not writable.", $this->screenshotDir));
        }

        if ($this->screenId == null) {
            throw new \Exception("You must provide a screen ID in behat.yml.");
        }

        //is this display available ?
        exec(sprintf("xdpyinfo -display %s >/dev/null 2>&1 && echo OK || echo KO", $this->screenId), $output);
        if (sizeof($output) == 1 && $output[0] == "OK") {
            //screen capture
            exec(sprintf("DISPLAY=%s import -window root %s/%s", $this->screenId, rtrim($this->screenshotDir, '/'), $filename), $output, $return);
            if (sizeof($output) != 1 || $output[0] !== "OK") {
                throw new \Exception(sprintf("Screenshot was not saved :\n%s", implode("\n", $output)));
            }
        }
        else {
            throw new \Exception(sprintf("Screen %s is not available.", $this->screenId));
        }
    }
}
