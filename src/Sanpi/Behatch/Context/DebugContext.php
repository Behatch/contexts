<?php

namespace Sanpi\Behatch\Context;

use Behat\Behat\Event\StepEvent;

class DebugContext extends BaseContext
{
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
     * @When /^I save a screenshot in "(?P<filename>[^"]*)"$/
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->saveScreenshot($filename);
    }

    /**
     * @AfterStep @javascript
     */
    public function failScreenshots(StepEvent $event)
    {
        if ($event->getResult() == StepEvent::FAILED) {
            $scenarioName = str_replace(' ', '_', $event->getStep()->getParent()->getTitle());
            $this->saveScreenshot(sprintf('fail_%s_%s.png', time(), $scenarioName));
        }
    }

    private function saveScreenshot($filename)
    {
        if (empty($filename)) {
            throw new \Exception('You must provide a filename for the screenshot.');
        }

        $screenshotDir = $this->getParameter('behatch.debug.screenshot_dir');
        $screenId = $this->getParameter('behatch.debug.screen_id');

        exec(sprintf('DISPLAY=%s import -window root %s/%s', $screenId, rtrim($screenshotDir, '/'), $filename), $output, $return);
        if ($return !== 0) {
            throw new \Exception(sprintf('Screenshot was not saved :\n%s', implode("\n", $output)));
        }
    }
}
