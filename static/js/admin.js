// Confirm Delete of Form
function confirmDelete(formId, name) {
    console.log("Confirm Delete has been called");
    var confirmation = confirm("Are you sure you want to delete " + name + "?");
    if (confirmation) {
        document.getElementById(formId).submit();
    }
}

function addEditFormValues() {
    var formData = new FormData();
    var xhr = new XMLHttpRequest();
    formData.append("product_id", document.getElementById("product_id").value);
    xhr.open("POST", "adminhome.php", true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            var response = xhr.responseText;
            var newData = JSON.parse(response);
            
            console.log(newData.price);
        }
    };
    xhr.send(formData);
}
