const dt = luxon.DateTime;
const dtNow = dt.now().setLocale('en-gb');
const calendar = document.getElementById('calendar');
const container = document.getElementById('container');
const weekdays = ['MON','TUE','WED','THUR','FRI','SAT','SUN'];
const basepath = '/KV6002/calendar';

// Function to create the grid for the calendar month view.

function createCalendarMonth(month, year, data) {
  calendar.setAttribute('class', 'calendar-month');
  calendar.insertAdjacentHTML('afterbegin', '<div id="day-grid"></div>');
  const dayGrid = document.getElementById('day-grid');

  let countEvents = data.count;
  let eventData = data.data;

  // date-time variables
  const date = luxon.DateTime.fromObject({'year':year, 'month':month});
  const monthStartWeekday = (date.startOf('month').weekday);
  const monthEndWeekday = (date.endOf('month').weekday);
  const prevMonthDays = monthStartWeekday-1;
  const nextMonthDays = 7-monthEndWeekday;
  const daysInMonth = date.daysInMonth;

  // returns true if day is a weekend
  const isWeekend = day => {
    return day % 7 === 6 || day % 7 === 0;
  }

  // return short month name from month number
  const monthName = num => {
    return luxon.DateTime.fromObject({'month':num}).monthShort;
  }

  // extra row added for months that start later in the week and overlap
  let calDays = 0;
  if((prevMonthDays + daysInMonth + nextMonthDays) > 35) {
    calDays = 42;
    dayGrid.style.gridTemplateRows = 'repeat(6, 1fr)';
  }
  else {
    calDays = 35;
  }

  // create a table for each day of the month
  for(let day=1; day <= calDays; day++) {
    // only display weekday-names in first row
    let dayName = "";
    if(day <= 7) {
      dayName = `<div class="weekday">${weekdays[day-1]}</div>`;
    }
    let overlapDays = date.minus({days:monthStartWeekday-day}).day; //days from other months in grid
    let overlapMonth = month;
    const weekend = isWeekend(day); // return true if weekend
    let dtID = "";

    // work out which days are from other months / overlap-days
    if ((day <= prevMonthDays || day > daysInMonth+prevMonthDays)) {
      overlapMonth = overlapDays < 7 ? overlapMonth+1 : overlapMonth-1;
      dtID = getDateToStr(incDate=true, incTime=false, 0, 0, dayInt=overlapDays, monthInt=overlapMonth, yearInt=year);
      overlapDays = overlapDays == 1 ? overlapDays+" "+monthName(overlapMonth) : overlapDays;
      dayGrid.insertAdjacentHTML('beforeend',
        `<div id="${dtID}" class="overlapDay ${weekend ? "weekend" : ""}">${dayName}${overlapDays}</div>`);
    }
    // else other days are in current month
    else {
      dayAdj = day-prevMonthDays;
      dtID = getDateToStr(incDate=true, incTime=false, 0, 0, dayInt=dayAdj, monthInt=month, yearInt=year);
      dayAdj = dayAdj == 1 ? dayAdj+" "+monthName(month) : dayAdj;
      dayGrid.insertAdjacentHTML('beforeend',
        `<div id="${dtID}" class="day ${weekend ? "weekend" : ""}">${dayName}${dayAdj}</div>`);
    }
  }

  eventData.forEach( (evt) => {
      let eventDT = dt.fromSQL(evt.start_date_time);
      let eventDay = document.getElementById(eventDT.toLocaleString());
      if(eventDay.childElementCount < 4) {
        eventDay.insertAdjacentHTML('beforeend', `<div id="eventID-${evt.event_id}" class="eventBoxDayGrid"><div class="event-click-container"></div>${evt.title}</div>`)
      } else {return}
      if(eventDay.childElementCount == 3) {
        eventDay.insertAdjacentHTML('beforeend', `<div id="events;${eventDT.toISODate()}" class="more-events-btn"><div class="more-events-container"></div>Click For More Events</div>`)
      }
  })
  getEventClassList(eventData);
}

// Function to create a time grid for the day and week calendar view.

function createCalendarTimeGrid(isDay, isWeek, day, month, year, data) {

  let countEvents = data.count;
  let eventData = data.data;

  let date = dt.fromObject({'day':day, 'month':month, 'year':year}).setLocale('en-gb');
  let dayCount = isDay ? 1 : 7;

  calendar.insertAdjacentHTML('afterbegin', '<div id="weekday-row"></div>');
  const weekdayRow = document.getElementById('weekday-row');
  weekdayRow.insertAdjacentHTML('beforeend', `<div class="row-timeCol"></div>`);
  calendar.insertAdjacentHTML('beforeend', '<div id="time-grid" role="grid"></div>');
  const timeGrid = document.getElementById('time-grid');
  timeGrid.insertAdjacentHTML('beforeend', '<div id="grid-hours" role="grid"></div>');
  const gridHours = document.getElementById('grid-hours');
  timeGrid.insertAdjacentHTML('beforeend', '<div id="grid-slots"></div>');
  const gridSlots = document.getElementById('grid-slots');

  if (isDay & !isWeek) {
    calendar.setAttribute('class', 'calendar-day');
    weekdayRow.style.gridTemplateColumns = '75px 1fr';
    weekdayRow.insertAdjacentHTML('beforeend', `<div class="weekday">${date.toLocaleString(dt.DATE_HUGE)}</div>`);
    timeGrid.style.gridTemplateColumns = '75px 1fr';
  }
  else if (isWeek & !isDay) {
    calendar.setAttribute('class', 'calendar-week');
    date = dt.fromObject({'day':date.startOf('week').day, 'month':month, 'year':year}).setLocale('en-gb');
    weekdays.forEach((name, i) => {
      let currentDay = date.plus({days:(i)}).day;
      weekdayRow.insertAdjacentHTML('beforeend', `<div class="weekday">${name}<br>${currentDay}</div>`);
    })
  }

  let hours = new Array(24);
  for(let hour=1; hour<=hours.length; hour++) {
    let hourAdj = hour == 1 ? "" : hour-1; // adjust loop value to correct hour value
    hourAdj = hourAdj != "" ? dt.fromObject({'hour':parseInt(hourAdj)}).toFormat('h a') : hourAdj;
    gridHours.insertAdjacentHTML('beforeend',`<div class="time-col">${hourAdj}</div>`);
  }

  gridSlots.insertAdjacentHTML('beforeend', `<div id="hour-slots"></div>`);
  hourSlots = document.getElementById('hour-slots');
  for(let day=0; day<dayCount; day++) {
    for(let hour=1; hour<=hours.length; hour++) {
      hourSlots.insertAdjacentHTML('beforeend',`<div class="time-slot"></div>`);
    }
  }

  gridSlots.insertAdjacentHTML('beforeend', `<div id="day-column-slots"></div>`);
  dayColumnSlots = document.getElementById('day-column-slots');
  for(let day=0; day<dayCount; day++) {
    dayColumnSlots.insertAdjacentHTML('beforeend',`<div id="event-col-day${day+1}" class="time-col-day"></div>`);
  }

  eventData.forEach( (evt) => {
    let limits = getEventLimits(evt);
    let colNum = isDay === true ? 1 : limits.weekday; // if current view is day -> all events go into column 1

    let eventStartHour = dt.fromSQL(evt.start_date_time).toLocaleString(dt.TIME_SIMPLE);
    let eventEndHour = dt.fromSQL(evt.end_date_time).toLocaleString(dt.TIME_SIMPLE);
    let ifSplit = limits.slotLength == 1 ? '' : '<br>';

    let eventText = `<div class="eventTitle">${evt.title}</div>${ifSplit}<div class="eventHours">${eventStartHour} - ${eventEndHour}</div>`;
    document.getElementById(`event-col-day${colNum}`).insertAdjacentHTML('beforeend', `<div id="eventID-${evt.event_id}" class="eventBoxTimeGrid"><div class="event-click-container"></div>${eventText}</div>`);
    const eventDiv = document.getElementById(`eventID-${evt.event_id}`);
    eventDiv.style.gridRowStart = limits.slotStart;
    eventDiv.style.gridRowEnd = limits.slotEnd;

    // if event box spans across a 15min block -> edit spacing so text is visible
    if(limits.slotLength == 1) {
      eventDiv.style.marginBottom = '1px';
      eventDiv.style.paddingTop = 0;
    }
  })
  getEventClassList(eventData);
}


// generate data from the event dates to position events correctly in calendar grid.
function getEventLimits(eventData) {
  let startDT = dt.fromSQL(eventData.start_date_time);
  let endDT = dt.fromSQL(eventData.end_date_time);
  let slotLimits = {};
  // NEED TO FIND OUT IF SOME EVENTS OVERLAP

  let interval = startDT.until(endDT);
  if(interval.isValid) {
    // console.log(interval);
    slotLimits.slotLength = (interval.length('hours')*4);
    slotLimits.slotStart = ((startDT.hour + (startDT.minute/60))*4)+1;
    slotLimits.slotEnd = ((endDT.hour + (endDT.minute/60))*4)+1;
    slotLimits.weekday = startDT.weekday;
    slotLimits.allDay = (eventData.all_day == '1');
    if(interval.count('day') > 1) {
      slotLimits.dayCount = interval.count('day');
      slotLimits.intervalObj = interval;
    }
    return slotLimits;
  }
  else {
    return undefined;
  }
}

// format date or time into locale string
let getDateToStr = (incDate=true, incTime=true, minute=0, hour=dtNow.hour, dayInt, monthInt, yearInt) => {
  //yyyy-mm-dd
  dateTime = "";
  if(incDate & !incTime){
    dateTime = dt.fromObject({
      'day':dayInt,
      'month':monthInt,
      'year':yearInt
    })
    .toLocaleString(dt.DATE_SHORT);
  }
  else if(incTime & !incDate) {
    dateTime = dt.fromObject({
      'minute':minute,
      'hour':hour
    })
    .toLocaleString(dt.TIME_24_SIMPLE);
  }
  else {
    dateTime = dt.fromObject(
      {'minute':minute,
      'hour':hour,
      'day':dayInt,
      'month':monthInt,
      'year':yearInt
    })
    .toLocaleString(dt.DATETIME_SHORT);
  }
  return dateTime.replace(/ /g, "");
}

/* Set the width of the side navigation to 250px and the left margin of the page content to 250px */
function openPanel() {
  document.getElementById("calendar-side-panel").style.width = "400px";
  document.getElementById("main").style.marginLeft = "400px";
  $('#panel-btn').hide();
}

/* Set the width of the side navigation to 0 and the left margin of the page content to 0 */
function closePanel() {
  document.getElementById("calendar-side-panel").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
  $('#panel-btn').show();
}

let dateHeading = document.getElementById('date-heading');


// *** MAIN FUNCTION ***
// get events and create calendar grid for specific view

function main(view, dtObj) {
  openPanel();

  let date = dtObj;
  let calendarView = view;
  let day = date.day;
  let month = date.month;
  let year = date.year;

  // get events using ajax
  $.ajax({
      url:`${basepath}/php/getEventsByDate.php`,
      type:"post",
      dataType:"json",
      data: {
        userID: userID,
        startDate: dt.local(year, month, day).startOf(calendarView).toSQL({includeOffset: false}),
        endDate: dt.local(year, month, day).endOf(calendarView).toSQL({includeOffset: false})
      },
      success: function(result) {
        switch(calendarView) {
          case 'day':
            $('#day-grid').remove();
            $('#weekday-row').remove();
            $('#time-grid').remove();
            createCalendarTimeGrid(isDay=true, isWeek=false, day, month, year, result);
            break;
          case 'week':
            $('#day-grid').remove();
            $('#time-grid').remove();
            $('#weekday-row').remove();
            createCalendarTimeGrid(isDay=false, isWeek=true, day, month, year, result);
            break;
          case 'month':
            $('#day-grid').remove();
            $('#weekday-row').remove();
            $('#time-grid').remove();
            createCalendarMonth(month, year, result);
            break;
        }
      },
      error: function() {
        console.log('Error: Failed to import event data.');
      }
  });
}

// when window loads/refreshes run main function
window.addEventListener('load', function() {
  main(currentView, updateDate);
})


// variables for side panel specific DOM elements

let currentView = calendar.classList.value.split('-')[1];
let viewBtn = Array.from(document.getElementsByClassName('view-btn'));
let updateDateBtn = Array.from(document.getElementsByClassName('change-date-btn'));

let updateDate = dt.fromISO(calendar.getAttribute('data-date')); // date updates as calendar controls change

let eventID = 0; // define event-ID
let userID = parseInt(document.getElementById('user_id').value);
let newEventFlag = 1; // is a new event currently being selected

viewBtn.forEach(b => {
    let view;
    b.addEventListener('click', e => {
      view = b.name;
      currentView = view;
      let date = updateDate.toISODate();
        if(!b.classList.contains('selected')) {
          history.pushState({view: view, date: date}, '', `${basepath}/${view}/${date}/`);
        }
        calendar.setAttribute('class', `calendar-${b.name}`);
        main(view, updateDate);
    });
});

// modify the date according to the view time-range
function modifyDate(button, view) {
  let date;
  if (button == 'next') {
    switch(view) {
      case 'day':
        date = updateDate.plus({day: 1});
        break;
      case 'week':
        date = updateDate.startOf('week').plus({week: 1});
        break;
      case 'month':
        date = updateDate.startOf('month').plus({month: 1});
        break;
    }
  }
  else if (button == 'previous') {
    switch(view) {
      case 'day':
        date = updateDate.minus({day: 1});
        break;
      case 'week':
        date = updateDate.startOf('week').minus({week: 1});
        break;
      case 'month':
        date = updateDate.startOf('month').minus({month: 1});
        break;
    }
  }
  return date;
}

// for next or previous button -> update dates and generate new calendar
updateDateBtn.forEach(b => {
    b.addEventListener('click', e => {
      currentView = calendar.classList.value.split('-')[1];
      let date = modifyDate(b.name, currentView);
      updateDate = date;
      let dateStr = date.toISODate();
      dateHeading.textContent = date.toLocaleString(dt.DATE_HUGE);
      history.pushState({date: date.toISODate(), view: currentView}, '', `${basepath}/${currentView}/${dateStr}/`);
        main(view=currentView, dtObj=date);
    });
});


// when today button is clicked the calendar for the current date is generated
let todayBtn = document.getElementById('today-btn');

todayBtn.addEventListener('click', e => {
  dateHeading.textContent = dtNow.toLocaleString(dt.DATE_HUGE);
  currentView = calendar.classList.value.split('-')[1];
  let currentDate = dtNow.toISODate();
  updateDate = dtNow;
  history.pushState({date: currentDate, view: currentView}, '', `${basepath}/${currentView}/${currentDate}/`);
    // document.title = 'Next';
    main(currentView, dtNow);
});

// when user clicks back button update calendar from history states
window.addEventListener('popstate', e => {
  let view = (e.state.view) === null ? currentView : (e.state.view);
  let date = (e.state.date) === null ? dtNow : dt.fromISO(e.state.date);
  main(view=view, dtObj=date);
});

history.replaceState({view: null, date: null}, 'Current Date', './');

// generate times for the drop-down list in the event form
let timeSelection = Array.from(document.getElementsByClassName('select-time'));

function getTimeOptions() {
  let timeArr = [];
  let time;
  let timeString = '';

  for(let h=0; h<=23; h++) {
    for(let m=0; m<=3; m++) {
      time = dt.fromObject({hour:h, minute:m*15});
      timeString12 = time.toLocaleString({ hour:'2-digit', minute:'2-digit', hour12:true});
      timeString24 = time.toFormat('HH:mm');
      timeArr.push({text:timeString12, value:timeString24});
    }
  }
  return timeArr;
}

let timeOptions = getTimeOptions();

timeSelection.forEach(selection => {
  timeOptions.forEach(option =>
    selection.add(new Option(option.text, option.value))
  );
})

// add event listner to all currently displayed event boxes
function getEventClassList(data) {
  let eventClickContainer = Array.from(document.querySelectorAll('.event-click-container'));
  eventClickContainer.forEach(c => {
    c.addEventListener('click', e => {
      newEventFlag = 0; // existing event so flag is set to 0
      let id = e.target.parentElement.id;
      eventID = parseInt(id.split('-')[1]);
      document.getElementById('delete-btn').disabled = false;
      document.getElementById('delete-btn').style.cssText = 'background-color:#f44336; cursor:pointer';
      setFormData(id, data);
    });
  });

  let moreEventsContainer = Array.from(document.querySelectorAll('.more-events-container'));
  moreEventsContainer.forEach(c => {
    c.addEventListener('click', e => {
      let dateString = e.target.parentElement.id.split(';')[1];
      let dateObj = dt.fromISO(e.target.parentElement.id.split(';')[1]);
      history.pushState({date: dateString, view: 'day'}, '', `${basepath}/day/${dateString}/`);
      main('day', dateObj);
    })
  })
}

// populate event form with currently selected event data
function setFormData(eventID, data) {
  let id = parseInt(eventID.split('-')[1]);
  data.forEach(e => {
    if(e.event_id == id) {
      document.getElementById('title-input').value = e.title;
      document.getElementById('location-input').value = e.location;
      let dtStart = dt.fromSQL(e.start_date_time);
      let dtEnd = dt.fromSQL(e.end_date_time);
      document.getElementById('date-input').value = dt.fromSQL(e.start_date_time).toISODate();
      let timeStart = dtStart.toLocaleString({ hour:'2-digit', minute:'2-digit', hour12:false});
      let timeEnd = dtEnd.toLocaleString({ hour:'2-digit', minute:'2-digit', hour12:false});
      $(`#time-start option[value='${timeStart}'`).prop("selected", true);
      $(`#time-end option[value='${timeEnd}'`).prop("selected", true);
      document.getElementById('event-description').value = e.description;
    }
  });
  openPanel();
}

// clear the event form and set up the submit button for a new event to be created
let createEventBtn = document.getElementById('create-event-btn');
createEventBtn.addEventListener('click', e => {
  newEventFlag = 1;
  document.getElementById('event-form').reset();
  document.getElementById('delete-btn').disabled = true;
  document.getElementById('delete-btn').style.cssText = 'background-color:grey; cursor:not-allowed';
});

// when event form submit button clicked check the dates are valid
$('#event-form-submit-btn').on("click", function(e) {
  e.preventDefault();
  checkEventDate($('#event-form').serializeArray());
});

// either update or create a new event in the DB
function eventFormSubmit() {
  let formData = new FormData($("#event-form")[0]);
  formData.append('user-id', userID);
  let path = '';
  if(newEventFlag == 1) {
    path = `${basepath}/php/createEvent.php`;
  } else {
    path = `${basepath}/php/updateEvents.php`;
    formData.append('event-id', eventID);
  }
  $.ajax({
      url: path,
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(e) {
        console.log(currentView);
        main(currentView, updateDate);
      },
      error: function() {
        console.log('Error: Failed to update database.');
      }
  });
}

// delete an event from the DB
$('#delete-btn').on("click", function(e) {
  e.preventDefault();
  $.ajax({
      url:`${basepath}/php/deleteEvent.php`,
      type:"post",
      data: {event_id:eventID},
      success: function() {
        main(currentView, updateDate);
      },
      error: function(error) {
        console.log('Error: Failed to delete event.');
      }
  });
});

// check that the events are not clashing and that times are valid
function checkEventDate(formArray) {
  let dateDay = formArray[2].value;
  let timeStart = formArray[3].value;
  let timeEnd = formArray[4].value;
  let formStartDT = dt.fromSQL(dateDay + " " + timeStart);
  let formEndDT = dt.fromSQL(dateDay + " " + timeEnd);

  console.log(formStartDT, formEndDT);

  if(formStartDT > formEndDT) {
    alert('Invalid Times Selected!');
    return false;
  }

  let invalid = false;
  $.ajax({
      url:`${basepath}/php/userEventsAll.php`,
      type:"post",
      dataType:"json",
      data: {
        userID: userID
      },
      success: function(result) {
        result.data.forEach(e => {
          if(eventID == e.event_id) {
            return
          } else {
            let dbStartDT = dt.fromSQL(e.start_date_time);
            let dbEndDT = dt.fromSQL(e.end_date_time);
            if(dt.max(formStartDT, dbStartDT) < dt.min(formEndDT, dbEndDT)) {
              invalid = true;
            }
          }
        })
        if(!invalid) {
          eventFormSubmit();
        }
        else {
          alert('Invalid Date. Overlaps with other event!');
        }
      },
      error: function() {
        console.log('Error: Failed to check form data.');
      }
  });
}

// logout button
let logoutBtn = document.getElementById('logout-btn');
logoutBtn.addEventListener('click', e => {
  e.preventDefault();
  $.ajax({
      url:`${basepath}/php/logoutProcess.php`,
      type:"post",
      success: function() {
        main(currentView, updateDate);
      },
      error: function(error) {
        console.log('Error: Failed to delete event.');
      }
  });
})

$('#logout-btn').click(function() {
  $.ajax({
    type: "POST",
    url:`${basepath}/php/logoutProcess.php`
  })
  .done(function() {
    setTimeout(function(){
      location.reload();
      return false;
    }, 0001);
  });
});
