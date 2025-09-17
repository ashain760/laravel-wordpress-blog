<template>
    <v-container fluid class="d-flex align-center justify-center fill-height">
        <v-card elevation="8" class="pa-6" max-width="400" width="100%">
            <div v-if="showLogin">
                <v-card-title class="text-center pb-4">
                    <div class="d-flex flex-column align-center">
                        <v-icon size="60" color="#21759b" class="mb-2">mdi-wordpress</v-icon>
                        <div class="text-h5 font-weight-medium">Login with WordPress</div>
                    </div>
                </v-card-title>
                <v-card-actions class="justify-center pt-4">
                    <v-btn
                        color="#21759b"
                        variant="elevated"
                        size="large"
                        @click="loginWithWordPress"
                        prepend-icon="mdi-wordpress"
                        class="text-white px-8"
                        block
                    >
                        Sign in with WordPress
                    </v-btn>
                </v-card-actions>
            </div>
            <center v-if="!showLogin">
                <v-progress-circular
                    indeterminate
                    color="primary"
                    size="60"
                ></v-progress-circular>
                <p style="font-size: 18px; font-weight: bold; margin-top: 10px;">Please wait, logging you in...</p>
            </center>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useSnackbar } from '../libs/snackbar/useSnackbar.js'
const { notify } = useSnackbar()
const loading = ref(false);

const loginWithWordPress = async () => {
    loading.value = true;
    try {
        const res = await axios.get("/wp/login");
        window.location.href = res.data.url;
    } catch (e) {
        console.error("Login error", e);
    } finally {
        loading.value = false;
    }
};

const router = useRouter();
const route = useRoute();
const showLogin = ref(true);

onMounted(async () => {

    const code = route.query.code;

    if (code) {
        showLogin.value = false;
        loading.value = true;
    }

    try {
        const res = await axios.get("/wp/callback", { params: { code} });
        router.replace("/blog-posts");
        notify(res.data.message, { color: 'success', duration: 4000 })
    } catch (err) {
        console.error("Callback error:", err.response?.data || err.message);
        router.replace("/");
    }
});

</script>

<style>
html,
body,
#app {
    height: 100%;
    margin: 0;
}
</style>
