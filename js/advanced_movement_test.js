var startButton = document.getElementById("start");
var buttonPressed = false;
var pressed = false;
var pressed2 = false;
var pressed3 = false;
var tm;
var tm2;
var tm3;
const minSpeed = 500; // минимальная скорость вращения
const maxSpeed = 800; // максимальная скорость вращения
var interval2;
var interval;
var interval3;
var results = [];
let resultPost
let test_id = 7
let correct = 0;

//-----------------------------------------------------------------

// Получить текущий угол поворота
(function($) {
$.fn.rotationDegrees = function() {
    var matrix = this.css("-webkit-transform") ||
    this.css("-moz-transform") ||
    this.css("-ms-transform") ||
    this.css("-o-transform") ||
    this.css("transform");
    if (typeof matrix === 'string' && matrix !== 'none') {
        var values = matrix.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180 / Math.PI));
    } else {
        var angle = 0;
    }
    return angle;
};
}(jQuery));

jQuery.fn.rotate = function(degrees) {
$(this).css({
    '-webkit-transform': 'rotate(' + degrees + 'deg)',
    '-moz-transform': 'rotate(' + degrees + 'deg)',
    '-ms-transform': 'rotate(' + degrees + 'deg)',
    'transform': 'rotate(' + degrees + 'deg)'
});
return $(this);
};



function updateTimer(timeLeft) {
// Таймер для обновления каждую секунду
var timer = setInterval(function() {
    timeLeft--;
    if (timeLeft >= 0) {
        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;
        var timeString = ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2); // форматировать время в виде "мм:сс"
        $("#timer").text(timeString);
    } else {
        clearInterval(timer);
        endGame();
    }
}, 1000);
}
//----------------------------------------------------------------------------------


function startGame() {
    var minutes = parseInt($("#minutes").val());
    var seconds = parseInt($("#seconds").val());
    //var minutes = 0
    //var seconds = 30
    if ((isNaN(minutes) || isNaN(seconds)) || (minutes < 0) || (minutes > 45) || (seconds < 0) || (seconds > 59)) {
        alert("Введите время от 1 до 45 минут.");
        buttonPressed = false;
        return;
    }
    $("#start").addClass("hidden");
    startButton.disabled = true;
    buttonPressed = true;
    inaccuracy = 0
    inaccuracy2 = 180
    inaccuracy3 = 90
    rotatePoint()
    rotatePoint2()
    rotatePoint3()
    var timeLeft = minutes * 60 + seconds;
    var timeString = ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2); // форматировать время в виде "мм:сс"
    $("#timer").text(timeString);
    updateTimer(timeLeft);
    $("#circle").rotate(rotationSpeed); // установить начальную скорость вращения
    $("#circle3").rotate2(rotationSpeed2); // установить начальную скорость вращения
    $("#circle5").rotate3(rotationSpeed3); // установить начальную скорость вращения
}


//----------------------------------------------------------------------------------
function endGame() {
    clearInterval(timer);
    save(results);
    $("#circle").addClass("hidden");
    $("#circle2").addClass("hidden");
    $("#container > p").addClass("hidden");
    $("#circle3").addClass("hidden");
    $("#circle4").addClass("hidden");
    $("#container2 > p").addClass("hidden");
    $("#circle5").addClass("hidden");
    $("#circle6").addClass("hidden");
    $("#container3 > p").addClass("hidden");
    $("#user-result").addClass("hidden");
    $("#user-result2").addClass("hidden");
    $("#user-result3").addClass("hidden");
    $("#end").removeClass("hidden");
}
//----------------------------------------------------------------------------------

function checkTime() {
    var timeoutId = setTimeout(function() {
        if (!pressed) {
            inaccuracy -= 30;
            rotatePoint();
        } else {
            pressed = false;
        }
    }, tm);
    return timeoutId;
}

function rotatePoint() {
    var speed = Math.floor(Math.random() * (maxSpeed - minSpeed + 1)) + minSpeed;
    if (inaccuracy !== undefined) {
        speed += inaccuracy;
    }
    $("#circle").rotate(speed);
    tm = (6000 / Math.max(speed - (inaccuracy || 0), minSpeed)) * 1000;
    if (inaccuracy === undefined) {
        inaccuracy = 0;
    } else {
        inaccuracy += 360;
    }
    checkTime();
}


function checkAnswer(){
var angle = $("#circle2").rotationDegrees();
var unghi = $("#circle").rotationDegrees();
if (unghi < angle + 30 && unghi > 0) {
    $("#result").text("+" + unghi + "°");
    results.push(unghi);
    inaccuracy -= unghi
    rotatePoint()
} else {
    if (unghi > angle - 30 && unghi < 0) {
        $("#result").text(unghi + "°");
        results.push(unghi);
        inaccuracy -= unghi
        rotatePoint()
    } else {
        if (unghi === 0) {
            $("#result").text(0);
            results.push(0);
            rotatePoint()
        } else if (unghi < 0) {
        inaccuracy -= unghi
        results.push(unghi);
        $("#result").text("Miss");
        rotatePoint()
        } else {
            inaccuracy += unghi
            results.push(unghi);            
            $("#result").text("Miss");
            rotatePoint()
        }
        
    }
}
}

//----------------------------------------------------------------

startButton.addEventListener("click", () => {
startGame();
});


//--------------------------------------------------------------------


// Получить текущий угол поворота
(function($) {
$.fn.rotationDegrees2 = function() {
    var matrix = this.css("-webkit-transform") ||
    this.css("-moz-transform") ||
    this.css("-ms-transform") ||
    this.css("-o-transform") ||
    this.css("transform");
    if (typeof matrix === 'string' && matrix !== 'none') {
        var values2 = matrix.split('(')[1].split(')')[0].split(',');
        var a2 = values2[0];
        var b2 = values2[1];
        var angle2 = Math.round(Math.atan2(b2, a2) * (180 / Math.PI));
    } else {
        var angle2 = 0;
    }
    return angle2;
};
}(jQuery));

jQuery.fn.rotate2 = function(degrees2) {
$(this).css({
    '-webkit-transform': 'rotate(' + degrees2 + 'deg)',
    '-moz-transform': 'rotate(' + degrees2 + 'deg)',
    '-ms-transform': 'rotate(' + degrees2 + 'deg)',
    'transform': 'rotate(' + degrees2 + 'deg)'
});
return $(this);
};


function checkTime2() {
    var timeoutId = setTimeout(function() {
        if (!pressed2) {
            inaccuracy2 -= 30;
            rotatePoint2();
        } else {
            pressed2 = false;
        }
    }, tm2);
    return timeoutId;
}

function rotatePoint2() {
    var speed2 = Math.floor(Math.random() * (maxSpeed - minSpeed + 1)) + minSpeed;
    if (inaccuracy2 !== undefined) {
        speed2 += inaccuracy2;
    }
    $("#circle3").rotate2(speed2);
    tm2 = (6000 / Math.max(speed2 - (inaccuracy2 || 0), minSpeed)) * 1000;
    if (inaccuracy2 === undefined) {
        inaccuracy2 = 180;
    } else {
        inaccuracy2 += 360;
    }
    checkTime2();
}


function checkAnswer2(){
    var angle2 = $("#circle4").rotationDegrees2();
    var unghi2 = $("#circle3").rotationDegrees2();
    if (unghi2 < -angle2 + 30 && unghi2 < 0) {
        $("#result2").text("+" + ((Math.abs(unghi2 + 180))) + "°");
        inaccuracy2 -= (Math.abs(unghi2 + 180))
        results.push(Math.abs(unghi2 + 180));
        rotatePoint2()
    } else {
        if (unghi2 > angle2 - 30 && unghi2 > 0) {
            $("#result2").text(unghi2 - 180 + "°");
            results.push(unghi2 - 180);
            inaccuracy2 -= (unghi2 - 180)
            rotatePoint2()
        } else {
            if (unghi2 === 180) {
                $("#result2").text(0);
                results.push(0);
                rotatePoint2()
            } else if (unghi2 < 0) {
                inaccuracy2 -= (unghi2 + 400)
                results.push(unghi2 - 180);
                $("#result2").text("Miss");
                rotatePoint2()
            } else {
                inaccuracy2 += (unghi2 - 180)
                results.push(Math.abs(unghi2 + 180));
                $("#result2").text("Miss");
                rotatePoint2()
            }
            
        }
}
}



//-----------------------------------------------------------

// Получить текущий угол поворота
(function($) {
    $.fn.rotationDegrees3 = function() {
        var matrix = this.css("-webkit-transform") ||
        this.css("-moz-transform") ||
        this.css("-ms-transform") ||
        this.css("-o-transform") ||
        this.css("transform");
        if (typeof matrix === 'string' && matrix !== 'none') {
            var values3 = matrix.split('(')[1].split(')')[0].split(',');
            var a3 = values3[0];
            var b3 = values3[1];
            var angle3 = Math.round(Math.atan2(b3, a3) * (180 / Math.PI));
        } else {
            var angle3 = 0;
        }
        return angle3;
    };
    }(jQuery));
    
    jQuery.fn.rotate3 = function(degrees3) {
    $(this).css({
        '-webkit-transform': 'rotate(' + degrees3 + 'deg)',
        '-moz-transform': 'rotate(' + degrees3 + 'deg)',
        '-ms-transform': 'rotate(' + degrees3 + 'deg)',
        'transform': 'rotate(' + degrees3 + 'deg)'
    });
    return $(this);
    };
    
    
    function checkTime3() {
        var timeoutId = setTimeout(function() {
            if (!pressed3) {
                inaccuracy3 -= 30;
                rotatePoint3();
            } else {
                pressed3 = false;
            }
        }, tm3);
        return timeoutId;
    }
    
    function rotatePoint3() {
        var speed3 = Math.floor(Math.random() * (maxSpeed - minSpeed + 1)) + minSpeed;
        if (inaccuracy3 !== undefined) {
            speed3 += inaccuracy3;
        }
        $("#circle5").rotate3(speed3);
        tm3 = (6000 / Math.max(speed3 - (inaccuracy3 || 0), minSpeed)) * 1000;
        if (inaccuracy3 === undefined) {
            inaccuracy3 = 90;
        } else {
            inaccuracy3 += 360;
        }
        checkTime3();
    }
    
    
    function checkAnswer3(){
        var angle3 = $("#circle6").rotationDegrees3();
        var unghi3 = $("#circle5").rotationDegrees3();
        if (unghi3 < angle3 + 30 && unghi3 > 90) {
            $("#result3").text("+" + ((Math.abs(unghi3 - 90))) + "°");
            inaccuracy3 += (Math.abs(unghi3) - 90)
            results.push(Math.abs(unghi3 - 90));
            rotatePoint3()
        } else {
            if (unghi3 > angle3 - 30 && unghi3 < 90) {
                $("#result3").text(unghi3 - 90 + "°");
                results.push(unghi3 - 90);
                inaccuracy3 += (unghi3 - 90)
                rotatePoint3()
            } else {
                if (unghi3 === 90) {
                    $("#result3").text(0);
                    results.push(0);
                    rotatePoint3()
                } else if ((unghi3 < 90) && (unghi3 > -90)) {
                    inaccuracy3 += (unghi3 - 360)
                    results.push(unghi3 - 90);
                    $("#result3").text("Miss");
                    rotatePoint3()
                } else {
                    inaccuracy3 -= (unghi3 + 500)
                    results.push(Math.abs(unghi3 - 90));
                    $("#result3").text("Miss");
                    rotatePoint3()
                }
                
            }
    }
    }

//----------------------------------------------------------


document.addEventListener("keydown", (event) => {
if (event.code === "KeyS" && !buttonPressed) {
    pressed = false;
    pressed2 = false;
    pressed3 = false;
    startButton.click();
} else if (buttonPressed) {
    if (event.code === "KeyW") {
        pressed = true;
        checkAnswer();
    } else if (event.code === "KeyD") {
        pressed2 = true;
        checkAnswer2();
    } else if (event.code === "KeyA") {
        pressed3 = true;
        checkAnswer3();
    }
}
});

function save(results) {
    // Вычисляем среднее значение из массива результатов
    const averageResult = calculateAverage(results);
    
    // Отправляем только среднее значение на сервер
    post('save_advanced_movement_test.php', { res: averageResult}, 'post');
}

function calculateAverage(results) {
    if (results.length === 0) {
        return 0; // Если массив результатов пуст, возвращаем 0
    }

    // Суммируем все результаты из массива
    const sum = results.reduce((acc, curr) => acc + curr, 0);
    
    // Вычисляем среднее значение
    const average = sum / results.length;

    return average;
}
 

function post(path, params, method='post') {
    const form = document.createElement('form');
    form.method = method;
    form.action = path;
     for (const key in params) {
      if (params.hasOwnProperty(key)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = key;
        hiddenField.value = params[key];
         form.appendChild(hiddenField);
      }
    }
     document.body.appendChild(form);
    const xhr = new XMLHttpRequest();
    xhr.open(method, path);
 
 
    const formData = new FormData();
    for (const key in params) {
    if (params.hasOwnProperty(key)) {
        formData.append(key, params[key]);
    }
    }
    xhr.send(formData);
 }