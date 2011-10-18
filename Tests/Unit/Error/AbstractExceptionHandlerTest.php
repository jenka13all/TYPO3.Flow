<?php
namespace TYPO3\FLOW3\Tests\Unit\Error;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Testcase for the Abstract Exception Handler
 *
 */
class AbstractExceptionHandlerTest extends \TYPO3\FLOW3\Tests\UnitTestCase {

	/**
	 * @test
	 * @return void
	 */
	public function handleExceptionLogsInformationAboutTheExceptionInTheSystemLog() {
		$exception = new \Exception('The Message', 12345);

		$mockSystemLogger = $this->getMock('TYPO3\FLOW3\Log\SystemLoggerInterface');
		$mockSystemLogger->expects($this->once())->method('logException')->with($exception);

		$exceptionHandler = $this->getMockForAbstractClass('TYPO3\FLOW3\Error\AbstractExceptionHandler', array(), '', FALSE);
		$exceptionHandler->injectSystemLogger($mockSystemLogger);
		$exceptionHandler->handleException($exception);
	}
}

?>