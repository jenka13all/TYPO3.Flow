<?php
declare(ENCODING = 'utf-8');
namespace F3\FLOW3\Command;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use \F3\FLOW3\MVC\CLI\Command;

/**
 * A Command Controller which provides help for available commands
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @scope singleton
 */
class HelpCommandController extends \F3\FLOW3\MVC\Controller\CommandController {

	/**
	 * @inject
	 * @var \F3\FLOW3\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @inject
	 * @var \F3\FLOW3\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * @inject
	 * @var \F3\FLOW3\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * @var array
	 */
	protected $commandsByPackagesAndControllers = array();

	/**
	 * Display help for a command
	 *
	 * The help command displays help for a given command:
	 * ./flow3 help
	 *
	 * @return string
	 */
	public function helpCommand() {
		$this->buildCommandsIndex();

		$context = $this->bootstrap->getContext();

		$this->response->appendContent("FLOW3 " . $this->packageManager->getPackage('FLOW3')->getPackageMetaData()->getVersion() . ' (' . $context . ')');
		$this->response->appendContent("usage: ./flow3" . ($context === 'Development' ? '_dev' : '') . " <command identifier>\n");
		$this->response->appendContent("The following commands are currently available:\n");

		foreach ($this->commandsByPackagesAndControllers as $packageKey => $commandControllers) {
			$this->response->appendContent(strtoupper($packageKey));
			foreach ($commandControllers as $commands) {
				$this->response->appendContent("");
				foreach ($commands as $command) {
					$this->response->appendContent("    " . str_pad($command->getCommandIdentifier(), 40) . $command->getShortDescription());
				}
			}
		}
		$this->response->appendContent("\nNOTE: Command line support is incomplete, passing arguments to commands is not yet implemented.\n");
	}

	/**
	 * Builds an index of available commands. For each of them a Command object is added to the commands array of this
	 * class.
	 *
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	protected function buildCommandsIndex() {
		$commandControllerClassNames = $this->reflectionService->getAllSubClassNamesForClass('F3\FLOW3\MVC\Controller\CommandController');
		foreach ($commandControllerClassNames as $className) {
			$class = new \F3\FLOW3\Reflection\ClassReflection($className);
			foreach ($class->getMethods() as $method) {
				$methodName = $method->getName();
				if (substr($methodName, -7, 7) === 'Command') {
					$command = new Command($className, substr($methodName, 0, -7));
					list($packageKey, $commandControllerName, $commandName) = explode(':', $command->getCommandIdentifier());
					$this->commandsByPackagesAndControllers[$packageKey][$commandControllerName][$commandName] = $command;
				}
			}
		}
	}
}
?>