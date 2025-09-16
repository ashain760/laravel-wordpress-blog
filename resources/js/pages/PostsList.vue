<template>
    <v-container fluid class="pa-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="d-flex align-center mb-4">
                <v-icon color="primary" size="100" class="mr-3">mdi-post-outline</v-icon>
                <div>
                    <h1 class="text-h3 font-weight-bold primary--text mb-1">Blog Management</h1>
                    <p class="text-subtitle-1 text--secondary ma-0">Manage your blog posts with ease</p>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="d-flex flex-wrap gap-3">
                <v-btn
                    color="success"
                    :loading="loading"
                    @click="refresh"
                    elevation="2"
                    class="px-6"
                    rounded
                >
                    <v-icon left>mdi-sync</v-icon> &nbsp;
                    Sync Posts
                </v-btn>
                <v-btn
                    color="primary"
                    @click="openCreate"
                    elevation="2"
                    class="px-6"
                    rounded
                >
                    <v-icon left>mdi-plus</v-icon> &nbsp;
                  Create New Post
                </v-btn>
                <v-btn
                    color="red"
                    variant="outlined"
                    elevation="2"
                    rounded
                    class="px-4 position-absolute"
                    style="top:42px; right:30px; background: #eb332612;"
                    @click="logout()"
                >
                    Logout &nbsp;
                  <v-icon right>mdi-login</v-icon>
                </v-btn>
            </div>
        </div>

        <!-- Search Section -->
        <v-card class="mb-6" elevation="2" rounded="xl">
            <v-card-text class="pa-6">
                <v-row align="center">
                    <v-col cols="12" >
                        <v-text-field
                            v-model="search"
                            label="Search posts by title..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            rounded
                            dense
                            hide-details
                            variant="plain"
                            class="fld-borded"
                        />
                    </v-col>
                    <v-col cols="12" md="6" lg="8">
                        <div class="d-flex align-center text--secondary">
                            <v-icon class="mr-2">mdi-information-outline</v-icon>
                            <span>{{ filteredPosts.length }} post{{ filteredPosts.length !== 1 ? 's' : '' }} found</span>
                        </div>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Posts Table -->
        <v-card elevation="4" rounded="xl" class="overflow-hidden">
            <v-card-title class="pa-6 pb-0">
                <h2 class="text-h5 font-weight-bold">Posts Overview</h2>
            </v-card-title>

            <v-data-table
                :headers="headers"
                :items="filteredPosts"
                item-key="id"
                :loading="loading"
                class="posts-table"
                loading-text="Loading posts..."
                no-data-text="No posts found"
                :items-per-page="10"
            >
                <template #item.title="{ item }">
                    <div class="d-flex align-center py-2">
                        <div>
                            <div class="mb-1">{{ item.title }}</div>
                        </div>
                    </div>
                </template>

                <template #item.content="{ item }">
                    <div class="content-preview" v-html="truncateContent(item.content)"></div>
                </template>

                <template #item.status="{ item }">
                    <v-chip
                        :color="item.status === 'publish' ? 'success' : 'warning'"
                        :text-color="item.status === 'publish' ? 'white' : 'black'"
                        small
                        class="font-weight-bold"
                    >
                        <v-icon left size="16">
                            {{ item.status === 'publish' ? 'mdi-check-circle' : 'mdi-clock-outline' }}
                        </v-icon>
                        {{ item.status === 'publish' ? 'Published' : 'Draft' }}
                    </v-chip>
                </template>

                <template #item.priority="{ item }">
                    <v-text-field
                        v-model="item.priority"
                        dense
                        hide-details
                        variant="plain"
                        type="number"
                        style="max-width: 80px;"
                        @blur="updatePriority(item)"
                        class="priority-field fld-borded"
                    />
                </template>

                <template #item.actions="{ item }">
                    <div class="d-flex gap-2">
                        <v-btn
                            icon
                            color="primary"
                            @click="edit(item)"
                            class="action-btn"
                            x-small
                        >
                            <v-icon small>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn
                            icon
                            color="error"
                            @click="confirmDelete(item)"
                            class="action-btn"
                            x-small
                        >
                            <v-icon small>mdi-delete</v-icon>
                        </v-btn>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <!-- Edit Modal -->
        <v-dialog v-model="editDialog" max-width="800px">
            <v-card>
                <v-card-title style="margin-left: 10px; padding-top: 15px; padding-bottom: 0px;">Edit Post</v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="editForm.title"
                        label="Title"
                        class="white-bg-input"
                    />

                    <p class="cnt">Content</p>
                    <QuillEditor
                        v-model:content="editForm.content"
                        content-type="html"
                        theme="snow"
                        style="height: 200px; margin-bottom: 20px;"
                    />
                    <v-select
                        v-model="editForm.status"
                        :items="['publish', 'draft']"
                        label="Status"
                        class="white-bg-input"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-btn
                           :loading="loadingSave"
                           @click="saveEdit"
                           elevation="2"
                           class="px-6 mb-3 bg-primary"
                           rounded
                    >
                        <v-icon left>mdi-content-save</v-icon> &nbsp; Save
                    </v-btn>
                    <v-btn
                        @click="editDialog = false"
                        elevation="2"
                        class="px-6 mb-3 bg-red"
                        rounded
                    >Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Create Modal -->
        <v-dialog v-model="createDialog" max-width="800px">
            <v-card>
                <v-card-title style="margin-left: 10px; padding-top: 15px; padding-bottom: 0px;">Create New Post</v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="createForm.title"
                        label="Title"
                        class="white-bg-input"
                    />
                    <p class="cnt">Content</p>
                    <QuillEditor
                        v-model:content="createForm.content"
                        content-type="html"
                        theme="snow"
                        style="height: 200px; margin-bottom: 20px;"
                    />
                    <v-select
                        v-model="createForm.status"
                        :items="['publish', 'draft']"
                        label="Status"
                        class="white-bg-input"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-btn
                           :loading="loadingSave"
                           @click="saveCreate"
                           elevation="2"
                           class="px-6 mb-3 bg-primary"
                           rounded
                    >
                        <v-icon left>mdi-content-save</v-icon> &nbsp; Create
                    </v-btn>
                    <v-btn
                        @click="createDialog = false"
                        elevation="2"
                        class="px-6 mb-3 bg-red"
                        rounded
                    >Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card rounded="xl" class="text-center">
                <v-card-text class="pa-8">
                    <v-icon color="error" size="64" class="mb-4">mdi-alert-circle</v-icon>
                    <h3 class="text-h5 font-weight-bold mb-3">Confirm Deletion</h3>
                    <p class="text-body-1 mb-0">
                        Are you sure you want to delete
                        <strong>"{{ deleteItem?.title }}"</strong>?
                    </p>
                    <p class="text-caption text--secondary mt-2">This action cannot be undone.</p>
                </v-card-text>

                <v-card-actions class="pa-6 pt-0">
                    <v-spacer />
                    <v-btn
                        text
                        @click="deleteDialog = false"
                        class="mr-3"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="error"
                        :loading="loadingSave"
                        @click="performDelete"
                        elevation="2"
                        class="px-6"
                    >
                        <v-icon left>mdi-delete</v-icon>
                        Delete Post
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const posts = ref([])
const search = ref('')
const loading = ref(false)
const loadingSave = ref(false)

const headers = [
    { title: 'Title', key: 'title', sortable: true, width: '25%' },
    { title: 'Content', key: 'content', sortable: false, width: '35%' },
    { title: 'Status', key: 'status', sortable: true, width: '15%' },
    { title: 'Priority', key: 'priority', sortable: true, width: '10%' },
    { title: 'Actions', key: 'actions', sortable: false, width: '15%' },
]

const statusOptions = [
    { text: 'Published', value: 'publish' },
    { text: 'Draft', value: 'draft' }
]

// dialogs & forms
const editDialog = ref(false)
const createDialog = ref(false)
const deleteDialog = ref(false)

const editForm = ref({ id: null, title: '', content: '', status: '', priority: 0 })
const createForm = ref({ title: '', content: '', status: 'draft', priority: 0 })
const deleteItem = ref(null)

// Helper function to truncate content
const truncateContent = (content) => {
    if (!content) return ''
    const stripped = content.replace(/<[^>]*>/g, '')
    return stripped.length > 120 ? stripped.substring(0, 120) + '...' : stripped
}

// fetch posts
const refresh = async () => {
    loading.value = true
    try {
        posts.value = (await axios.get('/api/posts')).data
    } catch (error) {
        console.error('Error fetching posts:', error)
        chkUnauthorized(error.status);
    } finally {
        loading.value = false
    }
}

// filtered posts by title
const filteredPosts = computed(() => {
    if (!search.value) return posts.value
    return posts.value.filter(p =>
        p.title.toLowerCase().includes(search.value.toLowerCase())
    )
})

// edit
const edit = (item) => {
    editForm.value = { ...item }
    editDialog.value = true
}
const saveEdit = async () => {
    loadingSave.value = true
    try {
        const { id, title, content, status, priority } = editForm.value
        await axios.put(`/api/posts/${id}`, { title, content, status, priority })
        editDialog.value = false
        await refresh()
    } catch (error) {
        console.error('Error saving post:', error)
        chkUnauthorized(error.status);
    } finally {
        loadingSave.value = false
    }
}

// create
const openCreate = () => {
    createForm.value = { title: '', content: '', status: 'draft', priority: 0 }
    createDialog.value = true
}
const saveCreate = async () => {
    loadingSave.value = true
    try {
        const { title, content, status, priority } = createForm.value
        await axios.post(`/api/posts`, { title, content, status, priority })
        createDialog.value = false
        await refresh()
    } catch (error) {
        console.error('Error creating post:', error)
        chkUnauthorized(error.status);
    } finally {
        loadingSave.value = false
    }
}

// delete with confirmation
const confirmDelete = (item) => {
    deleteItem.value = item
    deleteDialog.value = true
}
const performDelete = async () => {
    loadingSave.value = true
    try {
        await axios.delete(`/api/posts/${deleteItem.value.id}`)
        deleteDialog.value = false
        await refresh()
    } catch (error) {
        console.error('Error deleting post:', error)
        chkUnauthorized(error.status);
    } finally {
        loadingSave.value = false
    }
}

// update priority
const updatePriority = async (item) => {
    try {
        await axios.post(`/api/posts/${item.id}/priority`, { priority: item.priority })
        await refresh()
    } catch (error) {
        console.error('Error updating priority:', error)
        chkUnauthorized(error.status);
    }
}

const logout  = async () => {
    try {
       let res = (await axios.post(`/logout`))
       if(res.status == 200) {
           location.href = '/';
       }
    } catch (error) {
        console.error('Error updating priority:', error)
    }
}

const chkUnauthorized = (status) => {
    if(status == 401){
        location.href = '/';
    }
}

onMounted(refresh)
</script>

<style>

.cnt{
  margin-top: -8px;
  color: rgb(137, 137, 137);
  font-size: 14px;
  margin-bottom: 10px;
  margin-left: 16px;
}

.fld-borded{
    border-bottom: 2px #dcdcdc solid !important;
}

.v-data-table-header__content {
    font-weight: 700!important;
}

.posts-table {
    background: transparent;
}

.posts-table ::v-deep .v-data-table__wrapper {
    border-radius: 0;
}

.posts-table ::v-deep th {
    background: #f5f5f5;
    font-weight: 600;
    color: #333;
}

.posts-table ::v-deep tr:hover {
    background: #f9f9f9;
}

.content-preview {
    line-height: 1.4;
    color: #666;
}

.content-preview ::v-deep p {
    margin: 0;
}

.action-btn {
    transition: transform 0.2s;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    font-size: 12px !important;
    margin-right: 10px;
}

.action-btn:hover {
    transform: scale(1.1);
}

.priority-field ::v-deep .v-input__control {
    min-height: 32px;
}

/* Remove input field borders and underlines */
.priority-field ::v-deep .v-input__slot {
    border: none !important;
    box-shadow: none !important;
}

.priority-field ::v-deep .v-text-field__details {
    display: none !important;
}

.priority-field ::v-deep .v-input__append-inner {
    display: none !important;
}

.priority-field ::v-deep fieldset {
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
}

.priority-field ::v-deep .v-field__outline {
    display: none !important;
}

.priority-field ::v-deep .v-field__overlay {
    display: none !important;
}

.priority-field ::v-deep .v-field__input {
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    background: white !important;
    padding: 4px 8px !important;
    min-height: auto !important;
}

.editor-container {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.editor-container ::v-deep .ql-toolbar {
    border-bottom: 1px solid #ddd;
    background: #f8f9fa;
}

.editor-container ::v-deep .ql-container {
    border: none;
    font-size: 14px;
}

.gap-3 {
    gap: 12px;
}

/* Animation for dialogs */
.v-dialog {
    transition: all 0.3s ease;
}

/* Rounded corners for buttons */
.v-btn {
    text-transform: none;
    font-weight: 500;
}

.v-field__loader {
    display: none !important;
}

/* Remove all input underlines and borders */
.v-text-field ::v-deep .v-input__underline,
.v-text-field ::v-deep .v-input__slot::before,
.v-text-field ::v-deep .v-input__slot::after {
    display: none !important;
    border: none !important;
}

.white-bg-input ::v-deep .v-input__underline,
.white-bg-input ::v-deep .v-input__slot::before,
.white-bg-input ::v-deep .v-input__slot::after {
    display: none !important;
    border: none !important;
}

</style>
