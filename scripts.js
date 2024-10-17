document.getElementById("userForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const userId = document.getElementById("userId").value; //// Get user ID if updating
  const userData = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    password: document.getElementById("password").value,
  };
  let response;
  if (userId) {
    //udpate user
    response = await fetch(`../../phpcrud.php`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({ id: userId, ...userData }),// Send user ID and new data
    });
  } else {
     //create a new user
     response = await fetch(`../../phpcrud.php`, {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
         },
         body: JSON.stringify(userData)
     })

  }
  const result = await response.json();
  alert(result.message);
  loadUsers();
});

async function loadUsers(){
    const response = await fetch(`../../phpcrud.php`);
    const users = await response.json();
    const userList = document.getElementById('userList');
    userList.innerHTML = '';
    users.foreach(user => {userList.innerHTML += `<div>${user.name} - ${user.email} <button onclick="deleteUser(${user.id})">Delete</button>
        <button onclick="editUser(${user.id}, '${user.name}', '${user.email}')">Edit</button></div>`});
}


async function deleteUser(id){
    await fetch(`../../phpcrud.php?id=${id}`, {
        method: 'DELETE', // Send DELETE request
    });
    loadUsers();
}

function editUser(id,name,email){
    document.getElementById('userId').value = id; // Set user ID for update
    document.getElementById('name').value = name; // Set name in form
    document.getElementById('email').value = email; // Set email in form
    document.getElementById('password').value = ''; // Clear password field
}

// Load users on page load
loadUsers();
