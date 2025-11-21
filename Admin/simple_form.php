<!DOCTYPE html>
<html>
<head>
    <title>Simple Message Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        form {
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
            height: 100px;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <h1>Send Message - Simple Form</h1>
    
    <p>This is a very simple form that posts directly to send_message.php.</p>
    
    <form action="send_message.php" method="POST">
        <div>
            <label for="message">Your Message:</label>
            <textarea id="message" name="message">This is a test message</textarea>
        </div>
        
        <button type="submit">Send Message</button>
    </form>
    
    <p><a href="message.php">Back to Message Interface</a></p>
</body>
</html> 