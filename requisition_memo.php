<?php session_start();
require_once  'checkauth.php';
checkAuth();
include('api_config.php');
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <title>New Requisition</title>
    <style>
        .form-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .table-container {
            overflow-x: auto;
            max-width: 100%;
        }
        table {
            min-width: 100%;
        }
    </style>
</head>
<body>
    <!-- Include navigation -->
    <?php include("nav.php");
    //echo $_SESSION['my_ec'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $apiadd.'/place',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $_SESSION['token'] // Sending the token in the Authorization header
]
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
// Decode the JSON data
$data = json_decode($response, true);
$OriginCityOptions;
// Check if the status is success and data is available
if ($data['status'] === 'success' && !empty($data['data'])) {
    $CityOptions = '';
    // Loop through the data and create option elements
    foreach ($data['data'] as $item) {
        $CityOptions .= '<option value="' . htmlspecialchars($item['id']) . '">' . htmlspecialchars($item['description']) . '</option>';
    }
    $CityOptions .= '</select>';
} else {
    $CityOptions = 'No data available to load.';
}
    
    
    ?>

    <div class="container my-0 pb-4 form-container" style="background:#008B47;">
        <div class="shadow-lg container bg-white p-3 mb-5 bg-body rounded border">
            <h2 class="mb-4">NEW REQUISITION</h2>
           
            <form id="filterForm" action="submit_requisition.php" method="POST" onsubmit="return handleFormSubmission(event);">
            <input type="hidden" id="empArrayInput" name="empArray">
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Short Details Of Trip</label>
                    <input type="text" class="form-control" id="shortdetails" name="searchField" required placeholder="The Purpose of the trip">
                </div>
                <div class="form-group col-md-12 mb-3">
                    <label for="fulldetails" class="form-label">Details In Depth</label>
                    <textarea class="form-control" required id="fulldetails" name="fulldetails" rows="3"></textarea>
                </div>
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Origin</label>
                    <?php echo '<select class="form-control form-select" aria-label="Select city of Origin" id="origin" name="origin">'.$CityOptions; ?>
                </div>
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Destination</label>
                    <?php echo '<select class="form-control form-select" aria-label="Select Destination City" id="destination" name="destination">'.$CityOptions; ?>
                </div>
                <div class="col-md-12">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3 mb-3">
                            <label for="start_date" class="form-label">Departure Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start Date">
                        </div>
                        <div class="form-group col-md-3 mb-3">
                            <label for="start_time" class="form-label">Departure Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" required>
                        </div>
                        <div class="form-group col-md-3 mb-3">
                            <label for="end_date" class="form-label">Return Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" placeholder="Return Date">
                        </div>
                        <div class="form-group col-md-3 mb-3">
                            <label for="end_time" class="form-label">Return Arrival Time</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 pb-5">
                    <h5 class="strong">TEAM COMPOSITION</h5>
                    <div id="teamsTable" class="pb-5">                       
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EC Number</th>
                                    <th>Full Name</th>
                                    <th>Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- New rows will be added here -->
                            </tbody>
                        </table>
                        <strong>Add members to team</strong>
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group mb-0 col-md-5">
                                <input type="number" class="form-control" placeholder="E.C. Number" id="EcnuM" name="ecnumber" />
                            </div>
                            <div class="form-group mb-0 col-md-4">
                                <button type="button" class="btn btn-primary" onclick="addRow()">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 pb-5">
                    <h5 class="strong">RESOURCES BUDGET</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="TnS" onclick="toggleDiv()">
                        <label class="form-check-label" for="flexCheckDefault">Travel & Subsistence</label>
                        <div class="form-row align-items-end" id="RateTypes" hidden>
                            <!-- <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="RateTypes" id="inlineRadio1" value="unproven" onclick="toggleTnSDiv()">
                                <label class="form-check-label" for="inlineRadio1">Un-Proven Rates</label>
                            </div> 
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="RateTypes" id="inlineRadio2" value="proven" onclick="toggleTnSDiv()">
                                <label class="form-check-label" for="inlineRadio2">Proven Rates</label>
                            </div>-->
                            <input type="hidden" name="rateTypes" id="rateTypesInput" />
                        </div>
                    </div>
                    <div id="TnSDiv" hidden>
                        Dynamically Loaded
                    </div>
                </div>
                <div class="col-md-12 pb-5">
                    <button type="submit" class="btn btn-success">Submit Requisition Memo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <div id="resultsContainer" class="table-container mt-5 py-5 mb-5">
            <!-- Table will be loaded here by AJAX -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    <script>
        let emparray =[];
        $(document).ready(function() {
            firstec ="<?php echo $_SESSION['my_ec']; ?>"
            emparray.push(firstec)
            // console.log(emparray)
            addMetoRow(<?php echo $_SESSION['my_ec']; ?>);
            const today = new Date().toISOString().split('T')[0];
            $('#start_date, #end_date').val(today);
            $('#start_date, #end_date').attr('min', today);

            const currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let year = currentYear; year >= 2000; year--) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }
            $('#selectYear').html(yearOptions).val(currentYear);

            $('#start_date').on('change', function() {
                const startDate = $(this).val();
                $('#end_date').attr('min', startDate);
                $('#end_date').val(startDate);
             });

            //  $('#start_time').on('change', function(){
            //     const startTime = $(this).val();
            //     $('#end_time').attr('min', startTime);
            //     $('#end_time').val(startTime);
            //  })

        });

        function toggleTeamsDiv() {
            const teamsTable = document.getElementById('teamsTable');
            teamsTable.hidden = !document.getElementById('inlineRadio2').checked;
        }

        function addRow() {
            const table = document.getElementById('dataTable');
            const ecNumber = document.getElementById('EcnuM').value;
            
            if (!ecNumber) {
                alert("Please enter an E.C. Number.");
                return;
            }
            

            $.ajax({
                url: 'employee_helper.php', // PHP script to fetch the data
                type: 'POST',
                data: { ecnumber: ecNumber }, // Ensure this form exists and is correctly set up
                success: function(response) {
                    const data = response; // Populate the div with the response
                    var datax = JSON.parse(data);
                    if (datax.employee) {
                        datax.employee.forEach(emp => {
                            const newRow = table.insertRow();
                            const cell1 = newRow.insertCell(0);
                            const cell2 = newRow.insertCell(1);
                            const cell3 = newRow.insertCell(2);
                            const cell4 = newRow.insertCell(3);
                            cell1.innerHTML = table.rows.length - 1; // Row number
                            cell2.innerHTML = '<input type="text" class="form-control" name="name[]" placeholder="Enter name" value="' + emp.ecnumber + '" readonly/>';
                            cell3.innerHTML = emp.fullname;
                            cell4.innerHTML = emp.designation;
                            
                        });

                        emparray.push(ecNumber);
                    }
                    else{
                        alert ("No record found! Check EC number and try again");
                    }


                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                }
            });
            
            // Clear the E.C. Number input field
            document.getElementById('EcnuM').value = '';
        }
        function addMetoRow(ecNum) {
            const table = document.getElementById('dataTable');
            const ecNumber = ecNum;
            if (!ecNumber) {
                alert("Please enter an E.C. Number.");
                return;
            }
            
            $.ajax({
                url: 'employee_helper.php', // PHP script to fetch the data
                type: 'POST',
                data: { ecnumber: ecNumber }, // Ensure this form exists and is correctly set up
                success: function(response) {
                    const data = response; // Populate the div with the response
                    var datax = JSON.parse(data);
                    if (datax.employee) {
                        datax.employee.forEach(emp => {
                            const newRow = table.insertRow();
                            const cell1 = newRow.insertCell(0);
                            const cell2 = newRow.insertCell(1);
                            const cell3 = newRow.insertCell(2);
                            const cell4 = newRow.insertCell(3);
                            cell1.innerHTML = table.rows.length - 1; // Row number
                            cell2.innerHTML = '<input type="text" class="form-control" name="name[]" placeholder="Enter name" value="' + emp.ecnumber + '" readonly/>';
                            cell3.innerHTML = emp.fullname;
                            cell4.innerHTML = emp.designation;
                            
                        });
                    }
                    else{
                        alert ("akhula");
                    }


                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                }
            });
            
            // Clear the E.C. Number input field
            document.getElementById('EcnuM').value = '';
        }

        function validateDate_Time(){
                        // Get the input values for start and end time
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Check if start time and end time are not empty
            if (startTime === '' || endTime === '') {
                alert('Please fill in both the departure time and return time before proceeding.');
                return false; // Prevent running the rest of the function
            }

            if (startDate === endDate){
                if (startTime > endTime ){
                    alert('Return time cannot be less than departure time');
                    return false; // Prevent running the rest of the function
                }
            }



           return true;
        }



        function toggleDiv() {

            let res = validateDate_Time();
            // Proceed with toggling the div if inputs are filled
            //const rateTypesDiv = document.getElementById('RateTypes');
            const checkbox = document.getElementById('TnS');
            console.log(res);
            if(!res){
                checkbox.checked = false;
            }
            
            //if (rateTypesDiv) {
                if (checkbox.checked) {
                    //rateTypesDiv.removeAttribute('hidden'); // Show the div when checked
                    toggleTnSDiv();
                } else {
                    //rateTypesDiv.setAttribute('hidden', 'true'); // Hide the div when unchecked
                }
            //}
        }

        
    function toggleTnSDiv() {
            const rateTypesDiv = document.getElementById('TnSDiv'); // Ensure this ID matches your target div's ID
            //const radioOptions = document.getElementsByName('RateTypes');    
            rateTypesDiv.innerHTML = '<div style="text-align: center;"><img src="img/loading.gif" /></div>'; // Show loading GIF

            if (rateTypesDiv) {
                let selectedValue = '';
                /*for (let i = 0; i < radioOptions.length; i++) {
                    if (radioOptions[i].checked) {
                        selectedValue = radioOptions[i].value; // Get the value of the checked radio button
                        break;
                    }
                }*/
                selectedValue = "proven";
                document.getElementById('rateTypesInput').value = selectedValue;
                if (selectedValue === "unproven" || selectedValue === "proven") {
                    let employeeIds = emparray;

                    let travelStartDate = $('#start_date').val(); 
                    let travelStartTime = $('#start_time').val(); 
                    let travelEndDate = $('#end_date').val(); 
                    let travelEndTime = $('#end_time').val(); 
                    let isProven = (selectedValue === "proven");

                    $.ajax({
                        url: 'requisition_memo_helper.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            //action: 'fetch_and_curl', // Custom action parameter to guide PHP
                            employeeids: employeeIds,
                            travel_start_date: travelStartDate,
                            travel_start_time: travelStartTime,
                            travel_end_date: travelEndDate,
                            travel_end_time: travelEndTime,
                            is_proven: isProven
                        }),
                        success: function(response) {
                            rateTypesDiv.innerHTML = response; // Populate the div with the response
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error: ' + status + ' ' + error);
                            rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                        }
                    });
                    rateTypesDiv.removeAttribute('hidden'); // Show the div after the AJAX call
                } else if (selectedValue === "proven") {
                    rateTypesDiv.removeAttribute('hidden'); // Show the div for "proven"
                    rateTypesDiv.innerHTML = "<p>Tichaona</p>"; // Example content for proven case
                } else {
                    rateTypesDiv.setAttribute('hidden', 'true'); // Hide if none are selected
                    rateTypesDiv.innerHTML = ""; // Clear content
                }

            }
        }

    //     document.getElementById('filterForm').addEventListener('submit', function () {
       
    // });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function handleFormSubmission(event) {
  event.preventDefault(); // Prevent the default form submission
  validateDate_Time();
  console.log('We are here');
  
  // Get the form data
  document.getElementById('empArrayInput').value = JSON.stringify(emparray);
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);

  console.log(formData);
  
  fetch('submit_requisition.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text()) // or `response.json()` if expecting JSON
  .then(data => {
    console.log(data);
    Swal.fire({
      title: 'Success!',
      text: 'Memo saved successfully!',
      icon: 'success',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'my_requests.php'; // Redirect to another page
    });
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      title: 'Error!',
      text: 'There was an error processing your request.',
      icon: 'error',
      confirmButtonText: 'Try Again'
    });
  });

  return false; // Prevent the form from doing a full page submit
}
</script>




<div class="container my-0 pb-4 form-container" style="background:#008B47;">
        <div class="shadow-lg container bg-white p-3 mb-5 bg-body rounded border">
            <h2 class="mb-4">FUEL REQUISITION</h2>
           



            
            <form id="filterForm" action="submit_requisition.php" method="POST" onsubmit="return handleFormSubmission(event);">
            <input type="hidden" id="empArrayInput" name="empArray">
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Purpose of Fuel Request</label>
                    <input type="text" class="form-control" id="shortdetails" name="searchField" required placeholder="Short Details/Description Of the Trip">
                </div>
                <div class="form-group col-md-12 mb-3">
    <label for="fueltype" class="form-label">Fuel Type</label>
    <select class="form-control form-select" aria-label="Select Fuel Type" id="fueltype" name="fueltype">
        <option value="diesel">Diesel</option>
        <option value="petrol">Petrol</option>
    </select>
</div>
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Litres Required</label>
                    <input type="text" class="form-control" id="shortdetails" name="searchField" required placeholder="e.g. 35">
                </div>   
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Origin</label>
                    <?php echo '<select class="form-control form-select" aria-label="Select city of Origin" id="origin" name="origin">'.$CityOptions; ?>
                </div>
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Destination</label>
                    <?php echo '<select class="form-control form-select" aria-label="Select Destination City" id="destination" name="destination">'.$CityOptions; ?>
                </div>
                <div class="form-group col-md-12 mb-3">
                    <label for="shortdetails" class="form-label">Expected Distance (km)</label>
                    <input type="text" class="form-control" id="shortdetails" name="searchField" required placeholder="e.g. 35">
                </div>
                <div class="col-md-12">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3 mb-3">
                            <label for="start_date" class="form-label">Departure Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Date of Travel">
                        </div>
                        
            
                       
                    </div>
                </div>
                <div class="col-md-12 pb-5">
                    <h5 class="strong">TOLL GATE</h5>
                    <div id="teamsTable" class="pb-5">                       
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>EC Number</th>
                                    <th>Full Name</th>
                                    <th>Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- New rows will be added here -->
                            </tbody>
                        </table>
                        <strong>Add members to team</strong>
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group mb-0 col-md-5">
                                <input type="number" class="form-control" placeholder="E.C. Number" id="EcnuM" name="ecnumber" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 pb-5">
                    <button type="submit" class="btn btn-success">Submit Requisition Memo</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <div id="resultsContainer" class="table-container mt-5 py-5 mb-5">
            <!-- Table will be loaded here by AJAX -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    <script>
        let emparray =[];
        $(document).ready(function() {
            firstec ="<?php echo $_SESSION['my_ec']; ?>"
            emparray.push(firstec)
            // console.log(emparray)
            addMetoRow(<?php echo $_SESSION['my_ec']; ?>);
            const today = new Date().toISOString().split('T')[0];
            $('#start_date, #end_date').val(today);
            $('#start_date, #end_date').attr('min', today);

            const currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let year = currentYear; year >= 2000; year--) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }
            $('#selectYear').html(yearOptions).val(currentYear);

            $('#start_date').on('change', function() {
                const startDate = $(this).val();
                $('#end_date').attr('min', startDate);
                $('#end_date').val(startDate);
             });

            //  $('#start_time').on('change', function(){
            //     const startTime = $(this).val();
            //     $('#end_time').attr('min', startTime);
            //     $('#end_time').val(startTime);
            //  })

        });

        function toggleTeamsDiv() {
            const teamsTable = document.getElementById('teamsTable');
            teamsTable.hidden = !document.getElementById('inlineRadio2').checked;
        }

        function addRow() {
            const table = document.getElementById('dataTable');
            const ecNumber = document.getElementById('EcnuM').value;
            
            if (!ecNumber) {
                alert("Please enter an E.C. Number.");
                return;
            }
            

            $.ajax({
                url: 'employee_helper.php', // PHP script to fetch the data
                type: 'POST',
                data: { ecnumber: ecNumber }, // Ensure this form exists and is correctly set up
                success: function(response) {
                    const data = response; // Populate the div with the response
                    var datax = JSON.parse(data);
                    if (datax.employee) {
                        datax.employee.forEach(emp => {
                            const newRow = table.insertRow();
                            const cell1 = newRow.insertCell(0);
                            const cell2 = newRow.insertCell(1);
                            const cell3 = newRow.insertCell(2);
                            const cell4 = newRow.insertCell(3);
                            cell1.innerHTML = table.rows.length - 1; // Row number
                            cell2.innerHTML = '<input type="text" class="form-control" name="name[]" placeholder="Enter name" value="' + emp.ecnumber + '" readonly/>';
                            cell3.innerHTML = emp.fullname;
                            cell4.innerHTML = emp.designation;
                            
                        });

                        emparray.push(ecNumber);
                    }
                    else{
                        alert ("No record found! Check EC number and try again");
                    }


                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                }
            });
            
            // Clear the E.C. Number input field
            document.getElementById('EcnuM').value = '';
        }
        function addMetoRow(ecNum) {
            const table = document.getElementById('dataTable');
            const ecNumber = ecNum;
            if (!ecNumber) {
                alert("Please enter an E.C. Number.");
                return;
            }
            
            $.ajax({
                url: 'employee_helper.php', // PHP script to fetch the data
                type: 'POST',
                data: { ecnumber: ecNumber }, // Ensure this form exists and is correctly set up
                success: function(response) {
                    const data = response; // Populate the div with the response
                    var datax = JSON.parse(data);
                    if (datax.employee) {
                        datax.employee.forEach(emp => {
                            const newRow = table.insertRow();
                            const cell1 = newRow.insertCell(0);
                            const cell2 = newRow.insertCell(1);
                            const cell3 = newRow.insertCell(2);
                            const cell4 = newRow.insertCell(3);
                            cell1.innerHTML = table.rows.length - 1; // Row number
                            cell2.innerHTML = '<input type="text" class="form-control" name="name[]" placeholder="Enter name" value="' + emp.ecnumber + '" readonly/>';
                            cell3.innerHTML = emp.fullname;
                            cell4.innerHTML = emp.designation;
                            
                        });
                    }
                    else{
                        alert ("akhula");
                    }


                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ' ' + error);
                    rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                }
            });
            
            // Clear the E.C. Number input field
            document.getElementById('EcnuM').value = '';
        }

        function validateDate_Time(){
                        // Get the input values for start and end time
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Check if start time and end time are not empty
            if (startTime === '' || endTime === '') {
                alert('Please fill in both the departure time and return time before proceeding.');
                return false; // Prevent running the rest of the function
            }

            if (startDate === endDate){
                if (startTime > endTime ){
                    alert('Return time cannot be less than departure time');
                    return false; // Prevent running the rest of the function
                }
            }



           return true;
        }



        function toggleDiv() {

            let res = validateDate_Time();
            // Proceed with toggling the div if inputs are filled
            //const rateTypesDiv = document.getElementById('RateTypes');
            const checkbox = document.getElementById('TnS');
            console.log(res);
            if(!res){
                checkbox.checked = false;
            }
            
            //if (rateTypesDiv) {
                if (checkbox.checked) {
                    //rateTypesDiv.removeAttribute('hidden'); // Show the div when checked
                    toggleTnSDiv();
                } else {
                    //rateTypesDiv.setAttribute('hidden', 'true'); // Hide the div when unchecked
                }
            //}
        }

        
    function toggleTnSDiv() {
            const rateTypesDiv = document.getElementById('TnSDiv'); // Ensure this ID matches your target div's ID
            //const radioOptions = document.getElementsByName('RateTypes');    
            rateTypesDiv.innerHTML = '<div style="text-align: center;"><img src="img/loading.gif" /></div>'; // Show loading GIF

            if (rateTypesDiv) {
                let selectedValue = '';
                /*for (let i = 0; i < radioOptions.length; i++) {
                    if (radioOptions[i].checked) {
                        selectedValue = radioOptions[i].value; // Get the value of the checked radio button
                        break;
                    }
                }*/
                selectedValue = "proven";
                document.getElementById('rateTypesInput').value = selectedValue;
                if (selectedValue === "unproven" || selectedValue === "proven") {
                    let employeeIds = emparray;

                    let travelStartDate = $('#start_date').val(); 
                    let travelStartTime = $('#start_time').val(); 
                    let travelEndDate = $('#end_date').val(); 
                    let travelEndTime = $('#end_time').val(); 
                    let isProven = (selectedValue === "proven");

                    $.ajax({
                        url: 'requisition_memo_helper.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            //action: 'fetch_and_curl', // Custom action parameter to guide PHP
                            employeeids: employeeIds,
                            travel_start_date: travelStartDate,
                            travel_start_time: travelStartTime,
                            travel_end_date: travelEndDate,
                            travel_end_time: travelEndTime,
                            is_proven: isProven
                        }),
                        success: function(response) {
                            rateTypesDiv.innerHTML = response; // Populate the div with the response
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error: ' + status + ' ' + error);
                            rateTypesDiv.innerHTML = '<p>Error loading data. Please try again.</p>'; // Display error message
                        }
                    });
                    rateTypesDiv.removeAttribute('hidden'); // Show the div after the AJAX call
                } else if (selectedValue === "proven") {
                    rateTypesDiv.removeAttribute('hidden'); // Show the div for "proven"
                    rateTypesDiv.innerHTML = "<p>Tichaona</p>"; // Example content for proven case
                } else {
                    rateTypesDiv.setAttribute('hidden', 'true'); // Hide if none are selected
                    rateTypesDiv.innerHTML = ""; // Clear content
                }

            }
        }

    //     document.getElementById('filterForm').addEventListener('submit', function () {
       
    // });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function handleFormSubmission(event) {
  event.preventDefault(); // Prevent the default form submission
  validateDate_Time();
  console.log('We are here');
  
  // Get the form data
  document.getElementById('empArrayInput').value = JSON.stringify(emparray);
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);

  console.log(formData);
  
  fetch('submit_requisition.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text()) // or `response.json()` if expecting JSON
  .then(data => {
    console.log(data);
    Swal.fire({
      title: 'Success!',
      text: 'Memo saved successfully!',
      icon: 'success',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'my_requests.php'; // Redirect to another page
    });
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      title: 'Error!',
      text: 'There was an error processing your request.',
      icon: 'error',
      confirmButtonText: 'Try Again'
    });
  });

  return false; // Prevent the form from doing a full page submit
}
</script>





</body>
</html>
