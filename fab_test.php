<?php

?>
<html>
    <head>
        <?php include 'header_material.php'?>
        <style>
        .app-fab--absolute {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1;
        }
        
        #mini-options {
            height: auto;
            position: fixed;
            right: 1.5rem;
            z-index: 1;
            bottom: 5rem;
            transition: all, .5s;
        }
        
        #mini-options button {
            margin: .5rem 0;
            transition: all, .5s;
        }
        .off {
            transform: scale(0);
        }
        </style>
    </head>
    <body>
        <button class="mdc-fab app-fab--absolute material-fab " aria-label="menu_open" onclick="">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">menu_open</span>
        </button>
        <div id="mini-options">
            <button class="mdc-fab mdc-fab--mini material-fab off" aria-label="list_alt" onclick="">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">list_alt</span>
            </button>
            <br>
            <button class="mdc-fab mdc-fab--mini material-fab off" aria-label="report" onclick="">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">report</span>
            </button>
            <br>
            <button class="mdc-fab mdc-fab--mini material-fab off" aria-label="history" onclick="">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">history</span>
            </button>
            <br>
            <button class="mdc-fab mdc-fab--mini material-fab off" aria-label="exit_to_app" onclick="">
                <div class="mdc-fab__ripple"></div>
                <span class="mdc-fab__icon material-icons">exit_to_app</span>
            </button>
            <br>
        </div>
        <script>
            $('button').on('click', function(){
               $('#mini-options button').toggleClass("off");
                
            });
        </script>
    </body>
</html>

