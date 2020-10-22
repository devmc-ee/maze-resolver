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

        const rowsAmount = table.rows.length;
        const colsAmount = table.rows[0].cells.length;
        const wallsToPathsRatio = getWallsToPathsRatio();
        const minimalWallsAmountRatio = rowsAmount > 5 && colsAmount > 5 ? 0.25 : 0.175;

        const minWallsRequired = Math.ceil(rowsAmount * colsAmount * minimalWallsAmountRatio);
        if (rowsAmount > 3
            && colsAmount > 3
            && wallsToPathsRatio < minimalWallsAmountRatio) {
            mazeSolverResultContainer.innerHTML = `Please add some more walls (#). Minimum ${minWallsRequired}pcs is required. Just click somewhere on the maze.`;
            return null;
        }
        const mazeArray = getMazeArray();
        if (0 < mazeArray.length) {
            const data = {
                "maze": mazeArray
            }
            mazeSolverResultContainer.innerHTML = 'Analyzing....<div class="lds-dual-ring"></div>';
            makeRequest(data);
        } else {
            mazeSolverResultContainer.innerHTML = 'Please add some walls (#). Just click somewhere on the maze.';
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
                    mazeSolverResultContainer.innerHTML = 'Min. number of steps:' + response.routes[0].length + '<br>' +
                        'Found optimal routes: '+response.routes.length +  ' (click to highlight the route): ';

                    for (let route of response.routes) {

                        const routeBtn = document.createElement('button');
                        routeBtn.setAttribute('data-route', route);
                        routeBtn.value = route.toString();
                        routeBtn.innerHTML = route;
                        routeBtn.addEventListener('click', function (event) {
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
            let rowArray = [];
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

    rowsAmountSelect.addEventListener('change', function (event) {
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
     * Get ration of the set walls against total nodes amount.
     * It is a simple protection to make possible routes options as less as it is possible.
     * Otherwise runtime exceeds the allowed memory limits to process all options
     *
     * @returns {number}
     */
    function getWallsToPathsRatio() {

        let walls = 0;
        const rows = table.rows;
        const rowsAmount = rows.length;
        const colsAmount = rows[0].cells.length;
        let cols;
        for (let row of rows) {
            let rowArray = [];
            for (let col of row.cells) {
                let data = col.dataset;
                if ('#' === data.value) {
                    walls++;
                }
            }
        }

        return walls > 0 ? walls / (rowsAmount * colsAmount) : 0;
    }

    /**
     * Parse table for building maze array that is to be sent via ajax to the backend to find the oprimal route
     * @returns {[]|*[]}
     */
    function getMazeArray() {
        let maze = [];
        let walls = 0;
        const rows = table.rows;
        let cols;
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
        return walls > 0 ? maze : [];
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