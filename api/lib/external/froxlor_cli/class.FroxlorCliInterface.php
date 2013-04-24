<?php

class FroxlorCliInterface {

	/**
	 * static variable which holds all
	 * available commands for the froxlor-cli
	 *
	 * @var array
	 */
	private static $_commands = null;

	/**
	 * define version of Froxlor-CLI
	 *
	 * @var string
	 */
	private static $_version = '0.1';

	/**
	 * Froxlor API object
	 *
	 * @var Froxlor
	 */
	private $_api = null;

	/**
	 * main constructor of CLISystem class
	 *
	 * @param Froxlor $api
	 */
	public function __construct($api) {
		/**
		 * define available commands
		 */
		self::$_commands = array(
				'.help',			/* show help screen */
				'.info',			/* show infos about froxlorcli */
				'.version',			/* show version */
				'.quit'				/* exit the froxlorcli */
		);

		$this->_api = $api;
		$this->startShell();
	}

	/**
	 * main function to parse commands
	 *
	 * @param string $input shell-command
	 */
	private function _parseInput($input) {

		$input = trim($input);

		if($input == '') {
			$this->_unknownCommand();
			return;
		}

		if (strpos($input, " ") !== false) {
			$main_command = substr($input, 0, strpos($input, " "));
		} else {
			$main_command = $input;
		}

		if (substr($main_command, 0, 1) == '.' && !in_array($main_command, self::$_commands)) {
			$this->_unknownCommand($main_command);
			return;
		}

		switch ($main_command) {
			case '.help':
				$this->showHelp();
				break;
			case '.info':
				$this->showInfo();
				break;
			case '.version':
				$this->showVersion();
				break;
			case '.quit':
				echo "Goodbye!\n";
				exit;
			default:
				$inputparam = null;
				if (strlen($input) > strlen($main_command)) {
					$inputparam = substr($input, strlen($main_command)+1);
				}
				$this->parseApiCommand($main_command, $inputparam);
				break;
		}
	}

	/**
	 * tell the user that the command
	 * entered could not be interpretated
	 *
	 * @param string $command the command which is unknown
	 */
	private function _unknownCommand($command = '') {

		echo "Unknown command";
		if($command != '') {
			echo " '".$command."'";
		}
		echo "\n";
		echo "Type 'help' for a list of commands\n\n";
	}

	/**
	 * starts the cli session
	 */
	public function startShell() {
		echo "Starting Froxlor-CLI version ".self::$_version."...\n\n";
		echo "Type '.help' for a list of commands\n\n";
		$this->showPrompt();
	}

	/**
	 * shows the prompt for input
	 */
	public function showPrompt() {
		while (true) {
			echo "froxlor> ";
			$in = $this->_getInput();
			$this->_parseInput($in);
		}
	}

	/**
	 * function to get input from shell
	 */
	private function _getInput() {
		return trim(fgets(STDIN));
	}

	/**
	 * function to output a warning on the shell
	 *
	 * @param string|array $msg message to output, multiline if array
	 */
	public function ewarn($msg = null) {
		echo "\n*";
		if (!is_array($msg) && $msg != '') {
			echo "\n* ".$msg;
		} elseif (is_array($msg)) {
			foreach ($msg as $line) {
				echo "\n* ".$line;
			}
		} else {
			echo "\n* EMPTY WARNING! Should *not* happen!";
		}
		echo "\n*\n\n";
	}

	/**
	 * output help-screen
	 */
	public function showHelp() {
		$this->ewarn(array(
				".help\t\t\tshow this help-screen",
				".info\t\t\toutput information about Froxlor-CLI",
				".version\t\tshow version",
				".quit\t\t\texit the Froxlor-CLI",
				"(more to come in later versions)")
		);
	}

	/**
	 * output version
	 */
	public function showVersion() {
		$this->ewarn("Froxlor-CLI version ".self::$_version);
	}

	/**
	 * output information
	 */
	public function showInfo() {
		$this->ewarn(array(
				"Froxlor-CLI is a shell interface to the server-management-panel Froxlor",
				"",
				"It was designed to perform various Froxlor actions in case you either",
				"do not have access to the Froxlor webinterface anymore (webserver misconfigured)",
				"or you prefer working on a shell :)"
		)
		);
	}

	public function parseApiCommand($function, $params) {

		// build up parameter array
		$request_params = null;
		if ($params !== null) {
			$request_params = $this->_parseParams($params);
		}

		try {
			$req = ApiRequest::createRequest(
					$function,
					$request_params
			);
			$this->_api->sendRequest($req);
			$response = $this->_api->getLastResponse();

		} catch (ApiException $e) {
			$response = new ApiResponse(null);
			$_response = unserialize((string)$e);
			$response->addResponse($_response);
		}

		$rarr = $response->getResponse();

		if ($response->getResponseCode() != 200) {
			$err_arr = array(
					"There was an error in your request!",
					$rarr['header']['code'].': '.$rarr['header']['description']
			);
			if (isset($rarr['header']['detailed_messages'])) {
				if (is_array($rarr['header']['detailed_messages'])) {
					foreach ($rarr['header']['detailed_messages'] as $msg) {
						$err_arr[]=$msg;
					}
				} else {
					$err_arr[]=$rarr['header']['detailed_messages'];
				}
			}
			$this->ewarn($err_arr);
		} else {
			print_r($rarr['body']);
		}
	}

	private function _trimValue(&$val = null) {
		return trim($val);
	}

	private function _parseParams($inputline = null) {
		$result = array();
		$p = "/ (?=[\w]+\s*=)/";
		$fulltokens = preg_split($p, $inputline, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($fulltokens as $assign) {
			$ass_arr = explode("=", $assign);
			$val = trim($ass_arr[1]);
			if ((substr($val, 0, 1) == '"' && substr($val, -1) == '"')
					|| (substr($val, 0, 1) == "'" && substr($val, -1) == "'")
			) {
				$val = substr($val, 1, -1);
			}
			// array
			if (substr($val, 0, 1) == '{' && substr($val, -1) == '}') {
				$val = substr($val, 1, -1);
				$val = $this->_parseArray($val);
			}
			$result[$ass_arr[0]] = $val;
		}
		return $result;
	}

	/*
	 * TODO allow sub-arrays
	*
	* call parseParams recursivly when it calls us...yo dawg
	*/
	private function _parseArray($arrstring) {
		$parmarr = explode(",", $arrstring);
		$output = array();
		foreach ($parmarr as $name => $value) {
			$value = trim($value);
			$output = array_merge($output, $this->_parseParams($name.'='.$value));
		}
		return $output;
	}
}