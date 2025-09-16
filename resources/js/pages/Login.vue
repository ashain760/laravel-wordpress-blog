<template>
    <v-container fluid class="d-flex align-center justify-center fill-height">
        <v-card elevation="8" class="pa-6" max-width="400" width="100%">
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
                    :href="'/wp/login'"
                    prepend-icon="mdi-wordpress"
                    class="text-white px-8"
                    block
                >
                    Sign in with WordPress
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style>
html,
body,
#app {
    height: 100%;
    margin: 0;
}
</style>
