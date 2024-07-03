<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <input class="btnClass" type="button" name="button" value="Click Me" onclick="incrementLabel()">
    <label id="numberLabel">0</label>

    <div class="row">
        <input class="submitBtn" type="button" name="Select" value="Select" onclick="document.getElementById('fileInput').click();">
        <input type="file" id="fileInput" accept="image/*" style="display:none;" onchange="previewImage(event)">
    </div>

    <div class="row">
        <img id="preview" src="" style="padding: 5px; max-width: 100px; height: auto;">
    </div>

    <div id="tableButtons"></div>

    <div id="filters">
        <h3>Country</h3>
        <div id="countryFilters"></div>
        <h3>City</h3>
        <div id="cityFilters"></div>
    </div>
    
    <label for="recordsPerPage">Records per page:</label>
    <select id="recordsPerPage" onchange="fetchData()">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="100">100</option>
    </select>
    
    <table>
        <thead>
            <tr id="tableHeaders"></tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
    </table>

    <div class="pagination">
        <button onclick="previousPage()">Previous</button>
        <label id="currentPage">1</label>
        <button onclick="nextPage()">Next</button>
    </div>

    <script>
        let orderColumn = 'Name';
        let orderDirection = 'ASC';
        let countryFilter = [];
        let cityFilter = [];
        let currentPage = 1;
        let currentTable = 'employees';

        function incrementLabel() {
            let label = document.getElementById("numberLabel");
            let currentNumber = parseInt(label.innerText);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "increment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    label.innerText = xhr.responseText;
                }
            };
            xhr.send("number=" + currentNumber);
        }

        function previewImage(event) {
            let input = event.target;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function fetchTableNames() {
            fetch('fetch_table_names.php')
                .then(response => response.json())
                .then(data => {
                    const tableButtons = document.getElementById('tableButtons');
                    data.forEach(tableName => {
                        const button = document.createElement('button');
                        button.innerText = tableName;
                        button.onclick = () => {
                            currentTable = tableName;
                            fetchTableHeaders();
                            fetchFilters();
                            fetchData();
                        };
                        tableButtons.appendChild(button);
                    });
                });
        }

        function fetchTableHeaders() {
            fetch(`fetch_table_headers.php?table=${currentTable}`)
                .then(response => response.json())
                .then(headers => {
                    const tableHeaders = document.getElementById('tableHeaders');
                    tableHeaders.innerHTML = '';
                    headers.forEach(header => {
                        const th = document.createElement('th');
                        th.innerText = header;
                        th.onclick = () => sortTable(header);
                        tableHeaders.appendChild(th);
                    });
                });
        }

        function fetchFilters() {
            fetch('fetch_filters.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('countryFilters').innerHTML = data.countries.map(country => `<label><input type="checkbox" value="${country}" onchange="updateFilters('country', this)"> ${country}</label>`).join('<br>');
                    document.getElementById('cityFilters').innerHTML = data.cities.map(city => `<label><input type="checkbox" value="${city}" onchange="updateFilters('city', this)"> ${city}</label>`).join('<br>');
                });
        }

        function fetchData() {
            let formData = new FormData();
            formData.append('table', currentTable);
            formData.append('orderColumn', orderColumn);
            formData.append('orderDirection', orderDirection);
            formData.append('countryFilter', JSON.stringify(countryFilter));
            formData.append('cityFilter', JSON.stringify(cityFilter));
            formData.append('recordsPerPage', document.getElementById('recordsPerPage').value);
            formData.append('currentPage', currentPage);

            fetch('fetch_data.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let tableBody = document.getElementById('tableBody');
                tableBody.innerHTML = '';
                data.forEach(row => {
                    let tr = document.createElement('tr');
                    tr.innerHTML = Object.values(row).map(value => `<td>${value}</td>`).join('');
                    tableBody.appendChild(tr);
                });
                document.getElementById('currentPage').innerText = currentPage;
            });
        }

        function sortTable(column) {
            orderColumn = column;
            orderDirection = (orderDirection === 'ASC') ? 'DESC' : 'ASC';
            fetchData();
        }

        function updateFilters(type, checkbox) {
            if (type === 'country') {
                if (checkbox.checked) {
                    countryFilter.push(checkbox.value);
                } else {
                    countryFilter = countryFilter.filter(item => item !== checkbox.value);
                }
            } else if (type === 'city') {
                if (checkbox.checked) {
                    cityFilter.push(checkbox.value);
                } else {
                    cityFilter = cityFilter.filter(item => item !== checkbox.value);
                }
            }
            fetchData();
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                fetchData();
            }
        }

        function nextPage() {
            currentPage++;
            fetchData();
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchTableNames();
            fetchTableHeaders();
            fetchFilters();
            fetchData();
        });
    </script>
</body>
</html>
