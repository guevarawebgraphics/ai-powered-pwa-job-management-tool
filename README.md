# Appliance Repair American – Laravel + Vue.js PWA

**AI-powered Progressive Web App (PWA)** built using **Laravel 11** and **Vue.js**, designed for scheduling, communications, and intelligent automation in the appliance repair industry.

---

## 🚀 Project Overview

Appliance Repair American is a modern, scalable, AI-integrated platform built to optimize appliance repair operations. It includes:

-   📆 **Dynamic Calendar & Scheduling**
-   🤖 **AI Features** powered by OpenAI
-   📱 **Progressive Web App (PWA)** support
-   📞 **Twilio Integration** for SMS and voice
-   🔒 **API Authentication** using Laravel Sanctum
-   🛰️ **Real-Time Capabilities** with Laravel Reverb
-   🔧 **Developer-Friendly** with built-in tools and concurrency support

---

## 🧠 Core Technologies

| Layer         | Stack                              |
| ------------- | ---------------------------------- |
| Backend       | Laravel 11                         |
| Frontend      | Vue.js (via Vite)                  |
| AI            | OpenAI API via `openai-php/client` |
| Auth          | Laravel Sanctum                    |
| Real-Time     | Laravel Reverb                     |
| PWA           | silviolleite/laravelpwa            |
| Communication | Twilio SDK                         |

---

## 📦 Composer Dependencies

```json
{
    "laravel/framework": "^11.31",
    "google/auth": "^1.46",
    "openai-php/client": "^0.10.3",
    "openai-php/laravel": "^0.11.0",
    "laravel/sanctum": "^4.0",
    "laravel/reverb": "^1.4",
    "silviolleite/laravelpwa": "^2.0",
    "twilio/sdk": "^8.4"
}
```

## 🛠️ Dev Environment Setup

1. **Clone the repo**

```json
git clone https://github.com/yourusername/appliance-repair-american.git
cd appliance-repair-american
```

2. **Install PHP & JS deps**

```json
composer install
npm install
```

3. **Configure environment**
```json
cp .env.example .env
php artisan key:generate
```

4. **Run migrations**
```json
php artisan migrate
```


6. **Start development stack**

```json
composer run dev
```


---

## 📲 PWA Setup

This project uses **LaravelPWA** for offline support, a web app manifest, and mobile optimization.

- Configuration: `config/laravelpwa.php`
- Icons & metadata: `public/images/icons/`

---

## 🤖 AI & Advanced Features

Powered by **OpenAI** and custom tools for:

- Smart appointment suggestions
- AI-driven support chat (planned)
- Predictive dispatch optimization
- **Realtime conversation (speech-to-speech)** capabilities
- **File searching tool** for document retrieval

Make sure your `.env` includes:

OPENAI_API_KEY=your_openai_key_here

yaml
Copy
Edit

---

## 📡 Real-Time Events

Powered by **Laravel Reverb** to enable:

- Live booking updates
- Admin & technician notifications

---

## ☎️ Twilio Integration

Handle SMS reminders and voice features:

TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_PHONE=your_twilio_number

yaml
Copy
Edit

---

## 🧪 Testing & QA

Run tests with PHPUnit:

php artisan test

yaml
Copy
Edit

---

## 🧱 Built With

- [Laravel](https://laravel.com/)
- [Vue.js](https://vuejs.org/)
- [OpenAI PHP SDK](https://github.com/openai-php/client)
- [Twilio](https://www.twilio.com/)
- [LaravelPWA](https://github.com/silviolleite/laravel-pwa)

---

## 📃 License

This project is licensed under the [MIT license](LICENSE).

---

## 👥 Authors

**Monte Carlo Web Graphics** – AI-powered scheduling and field service efficiency.
```
