const apiKey = "your_actual_api_key_here"; // Replace with your OpenWeatherMap API key

document.getElementById("cityInput").addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    getWeather();
  }
});

async function getWeather() {
  const city = document.getElementById("cityInput").value.trim();
  const resultDiv = document.getElementById("weatherResult");

  if (!city) {
    alert("Please enter a city name");
    return;
  }

  const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;

  resultDiv.innerHTML = "⏳ Loading weather data...";

  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

    const data = await response.json();
    if (data.cod !== 200) {
      resultDiv.innerHTML = `❌ Error: ${data.message}`;
      return;
    }

    const iconCode = data.weather[0].icon;
    const iconUrl = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;

    resultDiv.innerHTML = `
      <h2>${data.name}, ${data.sys.country}</h2>
      <img src="${iconUrl}" alt="${data.weather[0].description}" />
      <p>🌡 Temperature: ${data.main.temp} °C</p>
      <p>🌤 Weather: ${data.weather[0].description}</p>
      <p>💨 Wind Speed: ${data.wind.speed} m/s</p>
    `;
  } catch (error) {
    console.error("Error fetching weather data:", error);
    resultDiv.innerHTML = "⚠️ Something went wrong while fetching the weather.";
  }
}
