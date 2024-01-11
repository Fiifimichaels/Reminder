const form = document.getElementById('reminder-form');
const table = document.getElementById('reminder-table').getElementsByTagName('tbody')[0];

// let reminders = <?php echo json_encode($reminders); ?>;
// const reminders = <?php echo json_encode($reminders); ?>;

// Add this function to calculate countdown
function calculateCountdown(endDate, elementId) {
    const endDateTime = new Date(endDate).getTime();
    const currentDateTime = new Date().getTime();
    const timeDifference = endDateTime - currentDateTime;

    const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

    const countdownString = `${days}d ${hours}h ${minutes}m ${seconds}s`;

    // Set the countdown in the corresponding table cell
    document.getElementById(elementId).innerHTML = countdownString;
}

function addReminder(event) {
    event.preventDefault();

    const no = document.getElementById('no').value;
    const carNo = document.getElementById('car-no').value;
    const description = document.getElementById('description').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    const reminder = {
        id: no,
        car_no: carNo,
        description,
        reg_date: startDate,
        expiry: endDate
    };

    reminders.push(reminder);

    renderTable();
    form.reset();
}

function renderTable() {
    table.innerHTML = '';

    reminders.forEach((reminder, index) => {
        const row = table.insertRow();

        const noCell = row.insertCell();
        noCell.innerHTML = reminder.id;

        const carNoCell = row.insertCell();
        carNoCell.innerHTML = reminder.car_no;

        const descriptionCell = row.insertCell();
        descriptionCell.innerHTML = reminder.description;

        const startDateCell = row.insertCell();
        startDateCell.innerHTML = reminder.reg_date;

        const endDateCell = row.insertCell();
        endDateCell.innerHTML = reminder.expiry;

        // Add a unique identifier to the countdown cell
        const countdownCell = row.insertCell();
        countdownCell.id = `countdown-${reminder.id}`;
        calculateCountdown(reminder.expiry, countdownCell.id);

        const editCell = row.insertCell();
        const editBtn = document.createElement('button');
        editBtn.classList.add('edit-btn');
        editBtn.innerHTML = 'Edit';
        editBtn.addEventListener('click', () => editReminder(index));
        editCell.appendChild(editBtn);

        const deleteCell = row.insertCell();
        const deleteBtn = document.createElement('button');
        deleteBtn.classList.add('delete-btn');
        deleteBtn.innerHTML = 'Delete';
        deleteBtn.addEventListener('click', () => deleteReminder(index));
        deleteCell.appendChild(deleteBtn);
    });
}

function editReminder(index) {
    const reminder = reminders[index];

    document.getElementById('no').value = reminder.id;
    document.getElementById('car-no').value = reminder.car_no;
    document.getElementById('description').value = reminder.description;
    document.getElementById('start-date').value = reminder.reg_date;
    document.getElementById('end-date').value = reminder.expiry;

    reminders.splice(index, 1);

    renderTable();
}

function deleteReminder(index) {
    reminders.splice(index, 1);

    renderTable();
}

form.addEventListener('submit', addReminder);

// Render the table on page load
renderTable();
