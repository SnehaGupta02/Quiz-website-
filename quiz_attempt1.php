<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style4_.css"> 
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h1>Quiz Attempt</h1>
    <div id="quiz-container"></div>
    <p id="total-marks"></p> <!-- Display total marks possible -->
    <button type="button" onclick="submitQuiz()">Submit</button>
    <p id="quiz-attempt-message"></p> <!-- Display quiz attempt message -->
  </div>

  <script>
    // Parse query parameters to get teacher name and quiz number
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const teacherName = urlParams.get('teacher');
    const quizNumber = parseInt(urlParams.get('quiz')); // Assuming quiz number is an integer
  
    // Fetch quiz data from the server based on teacher name and quiz number
    fetch(`fetch_quiz_data.php?teacher=${teacherName}&quiz=${quizNumber}`)
      .then(response => response.json())
      .then(quizData => {
        // Display quiz questions on the page
        displayQuizQuestions(quizData);
      })
      .catch(error => {
        console.error('Error fetching quiz data:', error);
      });
  
    // Keep track of selected options
    const selectedOptions = {};
  
    // Display quiz questions on the page
    function displayQuizQuestions(quizData) {
      const quizContainer = document.getElementById('quiz-container');
  
      // Initialize variables for total marks possible and total marks obtained
      let totalMarksPossible = 0;
      let totalMarksObtained = 0;
  
      // Loop through questions and display them
      quizData.forEach((question, index) => {
        const questionDiv = document.createElement('div');
        questionDiv.classList.add('question');
  
        // Add marks of current question to total marks possible
        totalMarksPossible += question.marks;
  
        // Create an image element to display the image
        const imageElement = document.createElement('img');
        imageElement.src = `data:image/jpeg;base64, ${question.imageData}`;
        imageElement.alt = 'Question Image';
        imageElement.style.maxWidth = '100%'; // Adjust image width as needed
  
        questionDiv.innerHTML = `
          <h3>Question ${index + 1}</h3>
          <p>${question.question}</p>
          ${question.imageData ? `<div>${imageElement.outerHTML}</div>` : ''}
          <p>Marks: ${question.marks}</p>
          <form id="options-form-${index}">
            ${question.options.map((option, optionIndex) => `
              <label>
                <input type="radio" name="options-${index}" value="${optionIndex + 1}">
                ${option}
              </label><br>
            `).join('')}
          </form>
          <p id="result-${index}" style="color: green;"></p>
        `;
  
        quizContainer.appendChild(questionDiv);
      });
  
      // Display total marks possible to the user
      document.getElementById('total-marks').textContent = `Total Marks: ${totalMarksPossible}`;
    }
  
    // Submit the quiz and display results
    function submitQuiz() {
      // Call submitQuizAttempt with teacherName and quizNumber as parameters
      submitQuizAttempt(teacherName, quizNumber);
    }
  
    // Submit the quiz attempt with teacherName and quizNumber as parameters
    function submitQuizAttempt(teacherName, quizNumber) {
      // Fetch quiz data from the server based on teacher name and quiz number
      fetch(`fetch_quiz_data.php?teacher=${teacherName}&quiz=${quizNumber}`)
        .then(response => response.json())
        .then(quizData => {
          // Initialize variables for total marks possible and total marks obtained
          let totalMarksPossible = 0;
          let totalMarksObtained = 0;
  
          // Loop through questions and check the selected options
          quizData.forEach((question, index) => {
            const optionsForm = document.getElementById(`options-form-${index}`);
            const selectedOption = optionsForm.querySelector(`input[name="options-${index}"]:checked`);
  
            // Display result below the question
            const resultDiv = document.getElementById(`result-${index}`);
            if (selectedOption) {
              const selectedOptionIndex = parseInt(selectedOption.value);
              if (selectedOptionIndex === question.correctOption) {
                resultDiv.textContent = `Marks Obtained: ${question.marks}`;
                // Add marks obtained for correct answer to total marks obtained
                totalMarksObtained += question.marks;
              } else {
                resultDiv.textContent = 'Marks Obtained: 0';
              }
            } else {
              resultDiv.textContent = 'Please select an option';
            }
  
            // Disable the options after submission
            optionsForm.querySelectorAll('input').forEach(input => {
              input.disabled = true;
            });
  
            // Save the selected option for reference
            selectedOptions[index] = selectedOption ? parseInt(selectedOption.value) : null;
  
            // Add marks of current question to total marks possible
            totalMarksPossible += question.marks;
          });
  
          // Display total marks obtained to the user
         
          // Send the quiz attempt data to the server for recording
          const formData = new FormData();
          formData.append('quizNumber', quizNumber);
          formData.append('totalMarks', totalMarksPossible); // Send total marks possible to PHP
          formData.append('totalMarksObtained', totalMarksObtained);
          formData.append('teacherName', teacherName);
          formData.append('studentName', getStudentName()); // Add student name to form data
          fetch('student_save_quiz.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(response => {
            console.log(response);
            const quizAttemptMessage = document.getElementById('quiz-attempt-message');
            if (response.hasOwnProperty('message')) {
              // If the response contains a 'message' property, it's a success message
              quizAttemptMessage.textContent = response.message;
              alert(`Total Marks Obtained: ${totalMarksObtained} out of ${totalMarksPossible}`);
            } else if (response.hasOwnProperty('error')) {
              // If the response contains an 'error' property, it's an error message
              quizAttemptMessage.textContent = response.error;
            } else {
              // Handle unexpected response
              quizAttemptMessage.textContent = 'Unexpected response from server.';
            }
          })
          .catch(error => {
            console.error('Error saving quiz attempt:', error);
            alert('Error saving quiz attempt. Please try again.');
          });
        })
        .catch(error => {
          console.error('Error fetching quiz data:', error);
          alert('Error fetching quiz data. Please try again.');
        });
    }

    // Function to get student's name
    function getStudentName() {
      // You can implement logic to retrieve the student's name, either from input or session
      // For example:
      // return document.getElementById('studentNameInput').value;
      return '<?php echo isset($_SESSION['studentName']) ? $_SESSION['studentName'] : ''; ?>';
    }
  </script>
  
</body>
</html>
