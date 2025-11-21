<!DOCTYPE html>
<html>
<head>
    <title>Test Message Sending</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            line-height: 1.6;
        }
        h1 {
            color: #333;
        }
        form {
            max-width: 500px;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Test Message Sending</h1>
    
    <p>This page tests direct form submission to send_message.php</p>
    
    <form id="testForm" action="send_message.php" method="POST">
        <div>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required>Test message from direct form</textarea>
        </div>
        
        <button type="submit">Send Test Message</button>
    </form>
    
    <div id="result"></div>
    
    <h2>Direct AJAX Test</h2>
    <form id="ajaxForm">
        <div>
            <label for="ajax-message">Message:</label>
            <textarea id="ajax-message" name="message" required>Test message from AJAX</textarea>
        </div>
        
        <button type="button" id="ajaxSend">Send via AJAX</button>
    </form>
    
    <div id="ajaxResult"></div>
    
    <p><a href="message.php">Back to Messages</a></p>
    
    <script>
        // AJAX test
        document.getElementById('ajaxSend').addEventListener('click', function() {
            var message = document.getElementById('ajax-message').value;
            
            if (!message.trim()) {
                alert('Please enter a message');
                return;
            }
            
            // Create XHR object
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            // Set up callback
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var resultDiv = document.getElementById('ajaxResult');
                    
                    try {
                        var response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            resultDiv.className = 'result success';
                            resultDiv.innerHTML = '<strong>Success!</strong> ' + response.message;
                        } else {
                            resultDiv.className = 'result error';
                            resultDiv.innerHTML = '<strong>Error:</strong> ' + response.message;
                            
                            // Add details if available
                            if (response.details) {
                                resultDiv.innerHTML += '<pre>' + JSON.stringify(response.details, null, 2) + '</pre>';
                            }
                        }
                    } catch (e) {
                        resultDiv.className = 'result error';
                        resultDiv.innerHTML = '<strong>Error parsing response:</strong> ' + e.message + 
                                             '<p>Raw response:</p><pre>' + xhr.responseText + '</pre>';
                    }
                }
            };
            
            // Send request
            xhr.send('message=' + encodeURIComponent(message));
        });
    </script>
</body>
</html> 