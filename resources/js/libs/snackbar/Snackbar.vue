<template>
    <v-snackbar
        v-model="visible"
        :timeout="timeout"
        :color="color"
        location="top"
        elevation="2"
    >
        {{ message }}
        <template #actions>
            <v-btn text @click="visible = false">Close</v-btn>
        </template>
    </v-snackbar>
</template>

<script setup>
import { useSnackbar } from './useSnackbar.js'

const { visible, message, color, timeout } = useSnackbar()
</script>
