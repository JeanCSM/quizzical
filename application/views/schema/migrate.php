<html>
<head>
    <title>Migrate</title>
    
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }
    
        body {
            background: #EEEEEE;
            font-family: Verdana, sans-serif;
            font-size: 12px;
            color: #666;
        }
        
        .container {
            background: #DDDDDD;
            margin: 100px auto;
            width: 370px;
            -moz-border-radius: 12px;
        }
        
        h1 {
            color: #333333;
            font-weight: normal;
            font-size: 22px;
            padding: 13px 15px;
            text-shadow: #fff 0 1px 0;
        }
        
        .submit-block {
            background: #ccc;
            padding: 13px 15px;
            -moz-border-radius-bottomleft: 12px;
            -moz-border-radius-bottomright: 12px;
        }
        
        fieldset {
            margin: 0 13px 15px 13px;
            border: 0;
            padding: 15px 0 0 0;
            border-top: 1px solid #999999;
        }
        
            legend {
                font-weight: bold;
                padding: 0 10px 0 0;
            }
            
            br {
                clear: both;
            }
            
            label {
                width: 75px;
                text-align: right;
                float: left;
                margin-right: 10px;
            }
            
            label, input.text, select, .text-field {
                margin-bottom: 10px;
            }
            
            input.checkbox {
                float: left;
                margin: 0 10px 10px 85px;
            }
            
            label.checkbox {
                float: none;
            }
        
        .messages {
            padding-bottom: 5px;
        }
        
            .message {
                margin-bottom: 10px;
                background: #666;
                color: #fff;
                padding: 10px 15px;
            }
    </style>
</head>

<body>
    <div class="container">
        <?php echo form_open(current_url()); ?>
            <div class="header-block">
                <h1>Migrate</h1>
            </div>
            
            <div class="messages">
                <?php foreach ($messages as $message): ?>
                <p class="message">
                    <?php echo $message; ?>
                </p>
                <?php endforeach; ?>
            </div>
            
            <fieldset>
                <legend>Versions</legend>
                
                <label>Current</label>
                <span class="text-field"><?php echo $version; ?></span>
                <br />
                
                <label>New</label>
                <?php echo form_dropdown("version", $versions, $latest); ?>
                <br />
                
                <input type="checkbox" class="checkbox" name="downgrades" />
                <label class="checkbox">Allow Downgrades</label>
                <br />
            </fieldset>
            
            <div class="submit-block">
                <input type="submit" class="button" value="Migrate" />
            </div>
        </form>
    </div>
</body>
</html>