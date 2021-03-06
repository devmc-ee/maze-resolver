;(function () {
    const rowsAmountSelect = document.querySelector('#rowsAmount');
    const columnsAmountSelect = document.querySelector('#columnsAmount');
    const mazeTableContainer = document.querySelector('.mazeTableContainer');
    const mazeSolverResultContainer = document.querySelector('.mazeSolverResultContainer');
    const mazeSubmitBtn = document.querySelector('#mazeSubmitBtn');
    let table = generateMazeTable(1, 1);
    mazeTableContainer.appendChild(table);

    const xhr = new XMLHttpRequest();

    /**
     * Event listener for submit button
     */
    mazeSubmitBtn.addEventListener('click', function (event) {
        event.preventDefault();

        const mazeArray = getMazeArray();
        if (0 < mazeArray.length) {
            const data = {
                "maze": mazeArray
            }
            mazeSolverResultContainer.innerHTML = 'Analyzing....<div class="lds-dual-ring"></div>';
            makeRequest(data);
        } else {
            mazeSolverResultContainer.innerHTML = 'OK! Please add some walls (#). Just click somewhere on the maze.';
        }

    });

    /**
     * Callback function for sending ajax request to server
     *
     * @param data
     * @returns {boolean}
     */
    function makeRequest(data) {

        if (!xhr) {
            mazeSolverResultContainer.innerHTML = "Sorry, looks like your browser doesn't support XMLHTTPRequest," +
                "that is required for the app.";
            return false;
        }
        const formData = new FormData();

        xhr.onreadystatechange = processResponse;
        xhr.open('POST', window.MazeSolver.ajaxurl)
        formData.set('maze', JSON.stringify(getMazeArray()))
        xhr.send(formData);
    }

    /**
     * Callback to process response from server
     */
    function processResponse() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (response.hasOwnProperty('routes')
                    && (response.routes.length > 0)) {

                    paintRouteOnMazeTable(response.routes[0])
                    mazeSolverResultContainer.innerHTML = 'Min. number of steps: <strong>' + response.routes[0].length + '</strong><br>' +
                        'Some optimal routes: <strong> '+response.routes.length +  '  </strong>(click to highlight the route): ';

                    for (let route of response.routes) {

                        const routeBtn = document.createElement('button');
                        routeBtn.setAttribute('data-route', route);
                        routeBtn.value = route.toString();
                        routeBtn.innerHTML = route;
                        routeBtn.addEventListener('click', function () {
                            paintRouteOnMazeTable(route)
                        });

                        mazeSolverResultContainer.appendChild(routeBtn);

                    }
                } else {
                    mazeSolverResultContainer.innerHTML = 'No solutions were found!<br>' + response.error;
                }
            } else {
                mazeSolverResultContainer.innerHTML = 'Woops!!! Something went wrong....<br>';
                const response = JSON.parse(xhr.responseText);
                console.error('Woops... Something went wrong.',response.error, response.message);

                mazeSolverResultContainer.innerHTML +=  response.error;

            }
        }
    }

    /**
     * Add color background to cells on the maze table to visualize the selected route.
     * @param route
     */
    function paintRouteOnMazeTable(route) {
        const rows = table.rows;

        for (let row of rows) {

            for (let col of row.cells) {
                let data = col.dataset;
                col.classList.remove('route-marker')
                if (route.includes(data.location)) {
                    col.classList.add('route-marker');

                }

            }

        }
        //paint start node
        rows[0].cells[0].classList.add('route-marker');
    }

    rowsAmountSelect.addEventListener('change', function () {
        columnsAmountSelect.disabled = false
        this.disabled = true;

    });
    columnsAmountSelect.addEventListener('change', function (event) {
        const rowsAmount = parseInt(rowsAmountSelect.value);
        const colsAmount = parseInt(event.target.value);

        if (0 < rowsAmount) {
            table = generateMazeTable(rowsAmount, colsAmount);

        } else {
            table = generateMazeTable(1, 1);
        }
        mazeTableContainer.innerHTML = '';
        mazeTableContainer.appendChild(table);
        mazeSubmitBtn.disabled = false;

    });



    /**
     * Parse table for building maze array that is to be sent via ajax to the backend to find the oprimal route
     * @returns {[]|*[]}
     */
    function getMazeArray() {
        let maze = [];
        let walls = 0;
        const rows = table.rows;

        for (let row of rows) {
            let rowArray = [];
            for (let col of row.cells) {
                let data = col.dataset;
                if ('#' === data.value) {
                    walls++;
                }
                rowArray.push(data.value);

            }
            maze.push(rowArray);
        }
        return  maze ;
    }

    /**
     * Generate maze table with set rows and columns
     * Add event listener to every cell
     * @param rows
     * @param cols
     * @returns {HTMLTableElement}
     */
    function generateMazeTable(rows = 1, cols = 1) {
        const endPoint = '' + (rows - 1) + '' + (cols - 1);
        const table = document.createElement('table');
        table.id = "mazeTable";
        table.classList.add('maze');
        let tableRow;
        for (let r = 0; r < rows; r++) {
            tableRow = table.insertRow(r);
            tableRow.setAttribute('data-row', r);

            for (let c = cols - 1; c >= 0; c--) {
                const tableCell = tableRow.insertCell(0);
                tableCell.setAttribute('data-col', c);
                tableCell.setAttribute('data-location', '' + r + c);
                tableCell.setAttribute('data-value', '.');
                tableCell.setAttribute('vertical-align', 'middle');
                tableCell.innerText = '.';
                tableCell.addEventListener('click', function (event) {
                    const nodeLocation = event.target.dataset.location;
                    if (nodeLocation !== endPoint
                        && '00' !== nodeLocation) {
                        addWall(event);
                    }

                });
            }

        }
        return table;
    }

    /**
     * Callback function to that is trigger when user click on a cell of the maze table. It changes value to #.
     * @param node
     */
    function addWall(node) {
        node.target.dataset.value = '#'
        node.target.innerText = '#';

    }
})();