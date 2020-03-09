const submitBtn = document.querySelector("#submit");
const firstname = document.querySelector("#firstname");
const surname = document.querySelector("#surname");


var sendDataForm = () => {
    if(!firstname.value || !surname.value) alert("Fill the form!");
    else {
        let data = {
            "firstname" : firstname.value,
            "surname" : surname.value
        }
        fetch('./api/submitForm.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then((response) => {
            if(response.status !== 200) {
                alert(`Server down :( HTTP status:  + ${response.status}`);
                return;
            }
            response.json()
            .then((data) => alert(`Witaj ${data.firstname} ${data.surname}!`))
        })
        .catch((err) => {
            alert(`Error: ${err}`);
        });
    }
}

submitBtn.addEventListener("click", sendDataForm);