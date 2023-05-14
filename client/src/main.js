import { createApp, ref } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')

const user = ref();
const token = ref();

const api = axios.create({
	baseUrl: '', // tutaj url laravela
});

api.interceptors.request.use((config) => {
	if (token !== undefined) {
		config.headers.Authorization = `Bearer ${token.value}`;
	}

	return config;
});

// funkcja do loginu
api.post("/login", {
	username: "blabla",
	password: "blablabla"
})
	.then(res => {
		token.value = res.data.token;
		user.value = res.data.user;
	});

api.get("/user")
	.then(res => {
		user.value = res.data;
	});
