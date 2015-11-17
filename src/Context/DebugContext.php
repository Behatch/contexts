<?php

namespace Sanpi\Behatch\Context;

use Behat\Gherkin\Node\StepNode;
use Behat\Behat\Hook\Scope\AfterStepScope;

class DebugContext extends BaseContext
{
    private $screenshotDir;

    public function __construct($screenshotDir = './screenshots')
    {
        $this->screenshotDir = $screenshotDir;
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     * Example: When I put a breakpoint
     * Example: Then I put a breakpoint
     * Example: And I put a breakpoint
     *
     * @Then (I )put a breakpoint
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
     * Example: When I save a screenshot in "logInView"
     * Example: Then I save a screenshot in "logInView"
     * Example: And I save a screenshot in "logInView"
     *
     * @When I save a screenshot in :filename
     */
    public function iSaveAScreenshotIn($filename)
    {
        sleep(1);
        $this->saveScreenshot($filename, $this->screenshotDir);
    }

    /**
     * @AfterStep
     */
    public function failScreenshots(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $scenario = $this->getScenario($scope);
            if ($scenario->hasTag('javascript')) {
                $scenarioName = urlencode(str_replace(' ', '_', $scope->getSuite()->getName()));
                $filename = sprintf('fail_%s_%s.png', time(), $scenarioName);
                $this->saveScreenshot($filename, $this->screenshotDir);
            }
        }
    }

    /**
     * @param AfterStepScope $scope
     * @return \Behat\Gherkin\Node\ScenarioInterface
     */
    private function getScenario(AfterStepScope $scope)
    {
        $scenarios = $scope->getFeature()->getScenarios();
        foreach ($scenarios as $scenario) {
            $stepLinesInScenario = array_map(
                function (StepNode $step) {
                    return $step->getLine();
                },
                $scenario->getSteps()
            );

            if (in_array($scope->getStep()->getLine(), $stepLinesInScenario)) {
                return $scenario;
            }
        }

        throw new \LogicException('Unable to find the scenario');
    }

}
