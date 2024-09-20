const login = document.querySelector(".login_btn");
const signup = document.querySelector(".signup_btn");
const loginForm = document.querySelector("#login_form");
const signupForm = document.querySelector("#signup_form");

login.addEventListener("click", () => {
  loginForm.style.display = "block";
  signupForm.style.display = "none";
  login.classList.add("active");
  signup.classList.remove("active");
});

signup.addEventListener("click", () => {
  loginForm.style.display = "none";
  signupForm.style.display = "block";
  signup.classList.add("active");
  login.classList.remove("active");

});
