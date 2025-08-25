const addBtns = document.querySelectorAll('.add-btn:not(.solid)');
const saveItemBtns = document.querySelectorAll('.solid');
const addItemContainers = document.querySelectorAll('.add-container');
const addItems = document.querySelectorAll('.add-item');
// Item Lists
const listColumns = document.querySelectorAll('.drag-item-list');
const backlogList = document.getElementById('backlog-list');
const progressList = document.getElementById('progress-list');
const completeList = document.getElementById('complete-list');
const onHoldList = document.getElementById('on-hold-list');

// Items
let updatedOnLoad = false;

// Initialize Arrays
let backlogListArray = [];
let progressListArray = [];
let completeListArray = [];
let onHoldListArray = [];
let listArrays = [];

// Drag Functionality
let draggedItem;
let currentColumn;
let dragging = false;

// Get Arrays from localStorage if available, set default values if not
function getSavedColumns() {
  if (localStorage.getItem('backlogItems')) {
    backlogListArray = JSON.parse(localStorage.backlogItems);
    progressListArray = JSON.parse(localStorage.progressItems);
    completeListArray = JSON.parse(localStorage.completeItems);
    onHoldListArray = JSON.parse(localStorage.onHoldItems);
  } else {
    backlogListArray = ['Release the course', 'Sit back and relax'];
    progressListArray = ['Work on projects', 'Listen to music'];
    completeListArray = ['Being cool', 'Getting stuff done'];
    onHoldListArray = ['Being uncool'];
  }
}

// Set localStorage Arrays
function updateSavedColumns() {
  listArrays = [
    backlogListArray, 
    progressListArray, 
    completeListArray, 
    onHoldListArray
  ];

  const arrayNames = ['backlog', 'progress', 'complete', 'onHold'];

  arrayNames.forEach((arrayName, index) => {    
    localStorage.setItem(`${arrayName}Items`, JSON.stringify(listArrays[index]));
  })  
}

function filterArray(array) {
  const filteredArray = array.filter(item => item !== null);
  return filteredArray;
}

// Create DOM Elements for each list item
function createItemEl(columnEl, column, item, index) {
  const listEl = document.createElement('li');
  listEl.classList.add('drag-item');
  listEl.textContent = item;
  listEl.draggable = true;
  listEl.contentEditable = true;
  listEl.id = index;
  listEl.setAttribute('ondragstart', 'drag(event)');
  listEl.setAttribute('onfocusout', `updateItem(${index}, ${column})`);
  columnEl.appendChild(listEl);
}

function drag(ev) {
  draggedItem = ev.target;  
  dragging = true;
}

function dragEnter(column) {
  listColumns[column].classList.add("over");
  currentColumn = column;
}

function dragLeave(column) {
  // listColumns[column].classList.remove("over");
}

function allowDrop(ev) {
  ev.preventDefault();  // o padrão é não permitir
}

function drop(ev) {
  ev.preventDefault();
  listColumns.forEach((column) => column.classList.remove("over"));

  const parent = listColumns[currentColumn];
  parent.appendChild(draggedItem);  

  draggedItem = null;
  dragging = false;
  rebuildArrays();
}

function rebuildArrays() {
  backlogListArray = Array.from(backlogList.children).map(i => i.textContent);
  progressListArray = Array.from(progressList.children).map(i => i.textContent);
  completeListArray = Array.from(completeList.children).map(i => i.textContent);
  onHoldListArray = Array.from(onHoldList.children).map(i => i.textContent);
  updateDOM();
}

// Update Columns in DOM - Reset HTML, Filter Array, Update localStorage
function updateDOM() {
  // Check localStorage once
  if (!updatedOnLoad) {
    getSavedColumns();
  }
  
  // Backlog Column
  backlogList.textContent = '';
  backlogListArray.forEach((item, index) => {
    createItemEl(backlogList, 0, item, index);
  })
  backlogListArray = filterArray(backlogListArray);

  // Progress Column
  progressList.textContent = '';
  progressListArray.forEach((item, index) => {
    createItemEl(progressList, 1, item, index);
  })
  progressListArray = filterArray(progressListArray);

  // Complete Column
  completeList.textContent = '';
  completeListArray.forEach((item, index) => {
    createItemEl(completeList, 2, item, index);
  })
  completeListArray = filterArray(completeListArray);

  // On Hold Column
  onHoldList.textContent = '';
  onHoldListArray.forEach((item, index) => {
    createItemEl(onHoldList, 3, item, index);
  })
  onHoldListArray = filterArray(onHoldListArray);

  // Run getSavedColumns only once, Update Local Storage
  updatedOnLoad = true;
  updateSavedColumns();
}

function showInputBox(column) {
  addBtns[column].style.visibility = 'hidden';
  saveItemBtns[column].style.display = 'flex';
  addItemContainers[column].style.display = 'flex';
}

function hideInputBox(column) {
  addBtns[column].style.visibility = 'visible';
  saveItemBtns[column].style.display = 'none';
  addItemContainers[column].style.display = 'none';
  addToColumn(column);
}

function addToColumn(column) {
  const itemText = addItems[column].textContent;
  const selectedArray = listArrays[column];
  selectedArray.push(itemText);
  addItems[column].textContent = '';
  updateDOM();
}

function updateItem(id, column) {
  const selectedArray = listArrays[column];
  const selectedColEl = listColumns[column].children;

  if (!dragging) {
    if (!selectedColEl[id].textContent) {
      delete selectedArray[id];
    } else {
      selectedArray[id] = selectedColEl[id].textContent;
    }
    updateDOM();
  }
}

// On Load
updateDOM();