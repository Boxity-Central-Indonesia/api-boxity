<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backend API for BOXITY ERP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
            background-color: #111;
            overflow: hidden;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .content {
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: orange;
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
            color: #fff;
            background-color: rgb(176, 117, 7);
            padding: .75rem;
            border-radius: .25rem;
        }

        .login-button {
            padding: 10px 20px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            /* Atur sesuai kebutuhan */
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div id="particles-js"></div>
    <div class="content">
        <img src="https://res.cloudinary.com/boxity-id/image/upload/v1678791551/asset_boxity/logo/logo_secondary_axlqck.png"
            alt="Logo Boxity" style="max-width: 250px; height: auto;">
        <h1>API Services for BOXITY ERP</h1>
        <p>Welcome to the API services for BOXITY ERP.<br>The API is active and running.</p>
        <button class="login-button" onclick="loginPrompt()">See the performance insights</button>
    </div>

    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ff9800"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                },
                "opacity": {
                    "value": 0.5,
                    "random": false,
                    "anim": {
                        "enable": false,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                        "enable": false,
                        "speed": 40,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ff9800",
                    "opacity": 0.4,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 6,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "repulse"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 140,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 400,
                        "size": 40,
                        "duration": 2,
                        "opacity": 8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200,
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true
        });
    </script>
    <script>
        function loginPrompt() {
            const email = prompt('Please enter your email:');
            const password = prompt('Please enter your password:');

            if (email && password) {
                // Membuat request login
                fetch('/api/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            email: email,
                            password: password
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.status == 200) {
                            // Login berhasil, redirect ke /telescope
                            window.location.href = '/telescope';
                        } else {
                            // Handle error login
                            alert('Login failed: ' + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            } else {
                alert('Email and password are required');
            }
        }
    </script>
</body>

</html>
