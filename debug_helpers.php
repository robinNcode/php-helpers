<?php
/**
 * Custom Debugging Function (d)
 *
 * This function provides a simple way to output debug information along with
 * contextual details such as the file, line, and function where it was called.
 * It is designed to be used during development and debugging phases.
 *
 * Usage:
 *   d($variable1, $variable2, ...);
 * @author MsM Robin
 * @param mixed ...$vars   The variables or values to be output for debugging.
 */
if (!function_exists('d')) {
    function d(...$vars) {
        // Get the calling function details
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $caller = $trace[1];

        // Extract file-related information
        $fileName = basename($caller['file']);
        $filePath = str_replace('/var/www/html/microfinnext-backend/application/', '', dirname($caller['file']));

        // Output debug information in a readable format
        echo '<pre>';
        foreach ($vars as $var) {
            echo "<h4 style='color: green;'>-> Calling from /$filePath/$fileName : line {$caller['line']}\n</h4>";
            print_r($var);
        }
        echo '</pre>';
    }
}

/**
 * Custom Debugging and Die Function (dd)
 *
 * This function is an extension of the custom debugging function (d).
 * It outputs debug information similar to d but additionally terminates
 * the script execution using die() immediately after the debug output.
 * It is useful for halting script execution during development for
 * detailed inspection of variables or program flow.
 *
 * Usage:
 *   dd($variable1, $variable2, ...);
 * @author MsM Robin
 * @param mixed ...$vars   The variables or values to be output for debugging.
 */
if (!function_exists('dd')) {
    function dd(...$vars)
    {
        // Call the custom debugging function (d) to output debug information
        d(...$vars);

        // Terminate script execution immediately after debug output
        die();
    }
}

/**
 * To print the names and values of dynamic variables passed to the function in plain text.
 * Supports arrays and objects.
 *
 * @param mixed ...$params
 */
if (!function_exists('np')) {
    function np(...$params)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $callingFunction = $backtrace[0];
        $callerFile = file($callingFunction['file']);
        $functionLine = trim($callerFile[$callingFunction['line'] - 1]);

        // Improved regex to handle more complex parameters
        preg_match('/np\((.*)\);/', $functionLine, $matches);

        if (!isset($matches[1])) {
            echo "Could not parse variable names.<br>";
            return;
        }

        $paramNames = array_map('trim', explode(',', $matches[1]));
        foreach ($paramNames as $index => $paramName) {
            $paramName = ltrim($paramName, '$');

            echo "<strong>$paramName</strong> = ";

            if (isset($params[$index])) {
                if (is_array($params[$index]) || is_object($params[$index])) {
                    echo "<pre>";
                    print_r($params[$index]);
                    echo "</pre>";
                } else {
                    echo htmlspecialchars($params[$index], ENT_QUOTES);
                }
            } else {
                var_dump($params[$index]);
            }

            echo " ";
        }

        echo "<br>";
    }
}



/**
 * To print the names and values of dynamic variables passed to the function in plain text
 * and terminate the script execution afterward.
 *
 * @param mixed ...$params
 */
if (!function_exists('npd')) {
    function npd(...$params)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $callingFunction = $backtrace[0];
        $callerFile = file($callingFunction['file']);
        $functionLine = trim($callerFile[$callingFunction['line'] - 1]);

        // Improved regex to handle complex cases
        preg_match('/npd\((.*)\);/', $functionLine, $matches);

        if (!isset($matches[1])) {
            echo "Could not parse variable names.<br>";
            die();
        }

        $paramNames = array_map('trim', explode(',', $matches[1]));
        foreach ($paramNames as $index => $paramName) {
            $paramName = ltrim($paramName, '$');

            echo "<strong>$paramName</strong> = ";

            if (isset($params[$index])) {
                if (is_array($params[$index]) || is_object($params[$index])) {
                    echo "<pre>";
                    print_r($params[$index]);
                    echo "</pre>";
                } else {
                    echo htmlspecialchars($params[$index], ENT_QUOTES);
                }
            } else {
                var_dump($params[$index]);
            }

            echo " ";
        }

        echo "<br>";
        die();
    }
}


/**
 * Measure the execution time of a code block using microtime().
 *
 * @param callable $codeBlock The code block to measure, passed as a callable.
 * @return float The execution time in seconds.
 */
if(!function_exists('mExeTime')){

    function mExeTime(callable $codeBlock, bool $die_status = false)
    {
        // Get the start time
        $startTime = microtime(true);

        // Execute the code block
        $codeBlock();

        // Get the end time
        $endTime = microtime(true);

        // Calculate and return the execution time
        $execution_time = $endTime - $startTime;
        np($die_status);
        if($die_status){
            npd($execution_time);
        }
        else{
            np($execution_time);
        }
    }
}

if(!function_exists('writeArrayToFile')){
    /**
     * Writes an array to a file at the specified path.
     * The array will be exported as valid PHP code using var_export().
     *
     * @param string $filePath The full path to the file where the array should be written.
     * @param array $contentArray The array content to write to the file.
     * @return bool True if the operation is successful, otherwise false.
     */
    function writeArrayToFile(string $filePath, array $contentArray, $variableName = 'contentArray'): bool
    {
        // Check if the file exists; if not, create it
        if (!file_exists($filePath)) {
            // Attempt to create the file
            if (touch($filePath)) {
                // Set 777 permissions
                chmod($filePath, 0777);
            } else {
                // Failed to create the file
                echo "Failed to create file: $filePath\n";
                return false;
            }
        }

        // Generate PHP code with var_export()
        $exportedContent = '<?php ' . $variableName . ' = ' . var_export($contentArray, true) . ';';

        // Attempt to write to the file
        if (file_put_contents($filePath, $exportedContent) !== false) {
            echo $filePath . ' written successfully!';
            return true;
        }
        else{
            echo $filePath . ' writing failed!';
        }

        // Writing failed
        return false;
    }
}