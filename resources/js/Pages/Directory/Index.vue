<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {usePage} from '@inertiajs/inertia-vue3';
import {computed, reactive} from 'vue';
import {router} from '@inertiajs/vue3';
import UiUserAvatar from "@/Components/Ui/UiUserAvatar.vue";
import {
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuPortal,
    DropdownMenuRoot,
    DropdownMenuTrigger,
} from 'radix-vue';

const page = usePage()


const props = defineProps({
    directory: Object
})

const handleFileUpload = (event) => {
    form.files = Array.from(event.target.files);
};


const form = reactive({
    commentary: null,
    service_id: null,
    equipment_id: null,
    sale_id: null,
    files: null
})

const parsedFiles = computed(() => {
    return JSON.parse(props.directory.files);
});

const submit = () => {
    const path = window.location.pathname;
    const parts = path.split('/');
    let type = parts[2];
    let id = parts[3];

    const formData = new FormData();
    formData.append('commentary', form.commentary);

    if (form.files) {
        form.files.forEach((file) => {
            formData.append('files[]', file);
        });
    }

    if (type === 'equipment') {
        formData.append('equipment_id', id);
    } else if (type === 'service') {
        formData.append('service_id', id);
    } else if (type === 'sale') {
        formData.append('sale_id', id);
    }

    router.post(`/directory/${type}/${id}`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });
};
</script>

<template>
    <AuthenticatedLayout bg="gray">
        <div class="p-6 text-2xl font-bold">Справочник</div>

        <div class="flex flex-col px-6 pb-6 lg:items-start lg:flex-row">
            <div class="py-5 px-2 content-block lg:mr-6 lg:w-[220px]">
                <div class="font-medium">Файлы:</div>
                <div class="mt-3 p-4 bg-bg1 text-xs">
                    <div v-for="file in parsedFiles" class="flex items-center max-w-full">
                        <span class="grow block mr-auto text-ellipsis overflow-hidden">{{ file.split('/')[1] }}</span>
                        <svg v-if="file.includes('.zip') || file.includes('.rar') || file.includes('.tar.gz')"
                             width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.83 6H19C19.7956 6 20.5587 6.31607 21.1213 6.87868C21.6839 7.44129 22 8.20435 22 9V17C22 17.7956 21.6839 18.5587 21.1213 19.1213C20.5587 19.6839 19.7956 20 19 20H5C4.20435 20 3.44129 19.6839 2.87868 19.1213C2.31607 18.5587 2 17.7956 2 17V7C2 6.20435 2.31607 5.44129 2.87868 4.87868C3.44129 4.31607 4.20435 4 5 4H10C11.306 4 12.417 4.835 12.83 6ZM19 8H11.415L10.944 6.666C10.8748 6.47105 10.7468 6.30233 10.5778 6.18307C10.4087 6.06381 10.2069 5.99985 10 6H5C4.73478 6 4.48043 6.10536 4.29289 6.29289C4.10536 6.48043 4 6.73478 4 7V17C4 17.2652 4.10536 17.5196 4.29289 17.7071C4.48043 17.8946 4.73478 18 5 18H19C19.2652 18 19.5196 17.8946 19.7071 17.7071C19.8946 17.5196 20 17.2652 20 17V9C20 8.73478 19.8946 8.48043 19.7071 8.29289C19.5196 8.10536 19.2652 8 19 8ZM16 10H18V12H16V10ZM14 8H16V10H14V8ZM14 12H16V14H14V12ZM16 14H18V16H16V14ZM14 16H16V18H14V16Z"
                                fill="#697077"/>
                        </svg>
                        <svg v-else class="shrink-0 block ml-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M6.75 3C6.35218 3 5.97064 3.15804 5.68934 3.43934C5.40804 3.72064 5.25 4.10218 5.25 4.5V19.5C5.25 19.8978 5.40804 20.2794 5.68934 20.5607C5.97064 20.842 6.35218 21 6.75 21H17.25C17.6478 21 18.0294 20.842 18.3107 20.5607C18.592 20.2794 18.75 19.8978 18.75 19.5V10.3712C18.75 10.3712 18.75 10.3711 18.75 10.3711C18.7499 10.1723 18.671 9.9817 18.5305 9.84111C18.5304 9.84109 18.5304 9.84107 18.5304 9.84105L11.9089 3.21961C11.7684 3.07905 11.5777 3.00006 11.3789 3C11.3789 3 11.3788 3 11.3788 3H6.75ZM4.62868 2.37868C5.19129 1.81607 5.95435 1.5 6.75 1.5H11.379C11.9755 1.50009 12.5476 1.73707 12.9695 2.15883L12.9695 2.15889L19.5912 8.78051C20.0129 9.20237 20.2499 9.77445 20.25 10.371V19.5C20.25 20.2956 19.9339 21.0587 19.3713 21.6213C18.8087 22.1839 18.0456 22.5 17.25 22.5H6.75C5.95435 22.5 5.19129 22.1839 4.62868 21.6213C4.06607 21.0587 3.75 20.2956 3.75 19.5V4.5C3.75 3.70435 4.06607 2.94129 4.62868 2.37868Z"
                                  fill="#697077"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M12 1.875C12.4142 1.875 12.75 2.21079 12.75 2.625V8.25C12.75 8.44891 12.829 8.63968 12.9697 8.78033C13.1103 8.92098 13.3011 9 13.5 9H19.125C19.5392 9 19.875 9.33579 19.875 9.75C19.875 10.1642 19.5392 10.5 19.125 10.5H13.5C12.9033 10.5 12.331 10.2629 11.909 9.84099C11.4871 9.41903 11.25 8.84674 11.25 8.25V2.625C11.25 2.21079 11.5858 1.875 12 1.875Z"
                                  fill="#697077"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M7.5 13.5C7.5 13.0858 7.83579 12.75 8.25 12.75H15.75C16.1642 12.75 16.5 13.0858 16.5 13.5C16.5 13.9142 16.1642 14.25 15.75 14.25H8.25C7.83579 14.25 7.5 13.9142 7.5 13.5Z"
                                  fill="#697077"/>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M7.5 17.25C7.5 16.8358 7.83579 16.5 8.25 16.5H15.75C16.1642 16.5 16.5 16.8358 16.5 17.25C16.5 17.6642 16.1642 18 15.75 18H8.25C7.83579 18 7.5 17.6642 7.5 17.25Z"
                                  fill="#697077"/>
                        </svg>
                        <DropdownMenuRoot>
                            <DropdownMenuTrigger aria-label="Customise options">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.5 6C13.5 6.82843 12.8284 7.5 12 7.5C11.1716 7.5 10.5 6.82843 10.5 6C10.5 5.17157 11.1716 4.5 12 4.5C12.8284 4.5 13.5 5.17157 13.5 6Z"
                                        fill="#687182"/>
                                    <path
                                        d="M13.5 12C13.5 12.8284 12.8284 13.5 12 13.5C11.1716 13.5 10.5 12.8284 10.5 12C10.5 11.1716 11.1716 10.5 12 10.5C12.8284 10.5 13.5 11.1716 13.5 12Z"
                                        fill="#687182"/>
                                    <path
                                        d="M13.5 18C13.5 18.8284 12.8284 19.5 12 19.5C11.1716 19.5 10.5 18.8284 10.5 18C10.5 17.1716 11.1716 16.5 12 16.5C12.8284 16.5 13.5 17.1716 13.5 18Z"
                                        fill="#687182"/>
                                </svg>
                            </DropdownMenuTrigger>

                            <DropdownMenuPortal>
                                <transition name="fade">
                                    <DropdownMenuContent
                                        class="py-2 px-1.5 rounded-md font-medium text-sm bg-white text-[#464F60] shadow-[0px_0px_0px_1px_rgba(152,_161,_179,_0.1),_0px_15px_35px_-5px_rgba(17,_24,_38,_0.2),_0px_5px_15px_rgba(0,_0,_0,_0.08)]"
                                        :side-offset="5" align="end">
                                        <DropdownMenuItem>
                                            <Link :href="'/'"
                                                  class="inline-flex items-center py-1 px-2 rounded hover:bg-my-gray transition-all">
                                                Скачать
                                                <svg class="block ml-2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                                                    <path fill="none" stroke="#464F60" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.004V17a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1M12 4.5v11m3.5-3.5L12 15.5L8.5 12"/>
                                                </svg>
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem>
                                            <Link
                                                :href="'/'"
                                                method="DELETE"
                                                class="inline-flex items-center py-1 px-2 rounded text-danger hover:bg-my-gray transition-all">
                                                Удалить
                                                <svg class="block ml-2" width="16" height="16"
                                                     viewBox="0 0 16 16" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                          d="M4.75 3.75V4.25H2.75C2.33579 4.25 2 4.58579 2 5C2 5.41421 2.33579 5.75 2.75 5.75H3.51389L3.89504 12.6109C3.95392 13.6708 4.8305 14.5 5.89196 14.5H10.108C11.1695 14.5 12.0461 13.6708 12.1049 12.6109L12.4861 5.75H13.25C13.6642 5.75 14 5.41421 14 5C14 4.58579 13.6642 4.25 13.25 4.25H11.25V3.75C11.25 2.50736 10.2426 1.5 9 1.5H7C5.75736 1.5 4.75 2.50736 4.75 3.75ZM7 3C6.58579 3 6.25 3.33579 6.25 3.75V4.25H9.75V3.75C9.75 3.33579 9.41421 3 9 3H7ZM7.25 7.75C7.25 7.33579 6.91421 7 6.5 7C6.08579 7 5.75 7.33579 5.75 7.75V12.25C5.75 12.6642 6.08579 13 6.5 13C6.91421 13 7.25 12.6642 7.25 12.25V7.75ZM10.25 7.75C10.25 7.33579 9.91421 7 9.5 7C9.08579 7 8.75 7.33579 8.75 7.75V12.25C8.75 12.6642 9.08579 13 9.5 13C9.91421 13 10.25 12.6642 10.25 12.25V7.75Z"
                                                          fill="currentColor"/>
                                                </svg>
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </transition>
                            </DropdownMenuPortal>
                        </DropdownMenuRoot>
                    </div>
                </div>
                <ul class="flex mt-2 -space-x-2">
                    <li>
                        <UiUserAvatar size="24px" class="border border-[#DDE1E6]"/>
                    </li>
                    <li>
                        <UiUserAvatar size="24px" class="border border-[#DDE1E6]"/>
                    </li>
                </ul>
                <div class="flex items-center justify-between mt-2 ">
                    <label class="cursor-pointer">
                        <input @change="handleFileUpload" type="file" name="files[]" multiple hidden>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.4 11.4V6.39999C13.4 6.13478 13.2946 5.88042 13.1071 5.69289C12.9196 5.50535 12.6652 5.39999 12.4 5.39999C12.1348 5.39999 11.8804 5.50535 11.6929 5.69289C11.5054 5.88042 11.4 6.13478 11.4 6.39999V11.4H6.39999C6.13478 11.4 5.88042 11.5054 5.69289 11.6929C5.50535 11.8804 5.39999 12.1348 5.39999 12.4C5.39999 12.6652 5.50535 12.9196 5.69289 13.1071C5.88042 13.2946 6.13478 13.4 6.39999 13.4H11.4V18.4C11.4 18.6652 11.5054 18.9196 11.6929 19.1071C11.8804 19.2946 12.1348 19.4 12.4 19.4C12.6652 19.4 12.9196 19.2946 13.1071 19.1071C13.2946 18.9196 13.4 18.6652 13.4 18.4V13.4H18.4C18.6652 13.4 18.9196 13.2946 19.1071 13.1071C19.2946 12.9196 19.4 12.6652 19.4 12.4C19.4 12.1348 19.2946 11.8804 19.1071 11.6929C18.9196 11.5054 18.6652 11.4 18.4 11.4H13.4Z"
                                fill="#697077"/>
                        </svg>
                    </label>

                </div>
            </div>
            <div class="grow">
                <div class="p-4 content-block">
                    <div class="font-bold text-lg">Общая информация:</div>
                    <div class="mt-6">
                        <label class="block text-sm">Комментарий</label>
                        <textarea
                            v-model="form.commentary"
                            class="block w-full h-[400px] mt-2 p-4 border border-[#C1C7CD] bg-bg1 outline-none focus:border-black lg:h-[600px]"
                        ></textarea>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button @click="submit" class="bg-my-gray text-side-gray-text font-bold px-6 py-3 ">Сохранить
                        </button>
                    </div>
                </div>

                <div class="flex items-center mt-4 p-3 border-l-[3px] border-l-gray1 bg-white">
                    <svg class="shrink-0 block mr-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.486 16.73C10.3547 16.7303 10.2247 16.7046 10.1034 16.6545C9.98206 16.6044 9.87182 16.5308 9.779 16.438L5.537 12.195C5.44143 12.1028 5.36517 11.9925 5.31268 11.8705C5.26018 11.7486 5.23251 11.6174 5.23126 11.4846C5.23001 11.3518 5.25522 11.2201 5.30542 11.0972C5.35561 10.9743 5.42978 10.8626 5.52361 10.7686C5.61744 10.6747 5.72903 10.6003 5.85189 10.55C5.97475 10.4996 6.10641 10.4742 6.23919 10.4752C6.37197 10.4763 6.50321 10.5038 6.62525 10.5561C6.74729 10.6084 6.85769 10.6845 6.95 10.78L10.485 14.315L16.85 7.95199C17.0375 7.76434 17.2919 7.65888 17.5571 7.65878C17.8224 7.65869 18.0769 7.76398 18.2645 7.95149C18.4521 8.13899 18.5576 8.39336 18.5577 8.65863C18.5578 8.9239 18.4525 9.17834 18.265 9.36599L11.193 16.438C11.1002 16.5308 10.9899 16.6044 10.8686 16.6545C10.7473 16.7046 10.6173 16.7303 10.486 16.73Z" fill="#697077"/>
                    </svg>
                    <div class="grow mr-2">
                        <b>Успешно сохранено.</b>
                        Справочник сохранен.
                    </div>
                    <button class="ml-auto" type="button">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.314 11.9L16.849 8.364C16.9445 8.27176 17.0207 8.16141 17.0731 8.03941C17.1255 7.9174 17.1531 7.78618 17.1543 7.6534C17.1554 7.52062 17.1301 7.38894 17.0798 7.26605C17.0295 7.14315 16.9553 7.0315 16.8614 6.93761C16.7675 6.84371 16.6559 6.76946 16.533 6.71918C16.4101 6.6689 16.2784 6.6436 16.1456 6.64475C16.0128 6.64591 15.8816 6.67349 15.7596 6.7259C15.6376 6.77831 15.5273 6.85449 15.435 6.95L11.899 10.485L8.364 6.95C8.27176 6.85449 8.16141 6.77831 8.03941 6.7259C7.9174 6.67349 7.78618 6.64591 7.6534 6.64475C7.52062 6.6436 7.38894 6.6689 7.26605 6.71918C7.14315 6.76946 7.0315 6.84371 6.93761 6.93761C6.84371 7.0315 6.76946 7.14315 6.71918 7.26605C6.6689 7.38894 6.6436 7.52062 6.64475 7.6534C6.64591 7.78618 6.67349 7.9174 6.7259 8.03941C6.77831 8.16141 6.85449 8.27176 6.95 8.364L10.485 11.899L6.95 15.435C6.85449 15.5273 6.77831 15.6376 6.7259 15.7596C6.67349 15.8816 6.64591 16.0128 6.64475 16.1456C6.6436 16.2784 6.6689 16.4101 6.71918 16.533C6.76946 16.6559 6.84371 16.7675 6.93761 16.8614C7.0315 16.9553 7.14315 17.0295 7.26605 17.0798C7.38894 17.1301 7.52062 17.1554 7.6534 17.1543C7.78618 17.1531 7.9174 17.1255 8.03941 17.0731C8.16141 17.0207 8.27176 16.9445 8.364 16.849L11.899 13.314L15.435 16.849C15.5273 16.9445 15.6376 17.0207 15.7596 17.0731C15.8816 17.1255 16.0128 17.1531 16.1456 17.1543C16.2784 17.1554 16.4101 17.1301 16.533 17.0798C16.6559 17.0295 16.7675 16.9553 16.8614 16.8614C16.9553 16.7675 17.0295 16.6559 17.0798 16.533C17.1301 16.4101 17.1554 16.2784 17.1543 16.1456C17.1531 16.0128 17.1255 15.8816 17.0731 15.7596C17.0207 15.6376 16.9445 15.5273 16.849 15.435L13.314 11.899V11.9Z" fill="#21272A"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

<!--        <div class="flex h-screen bg-gray-100">
            &lt;!&ndash; Sidebar &ndash;&gt;
            <div class="w-1/4 bg-white p-4 shadow-md">
                <h2 class="text-lg font-bold mb-4">Справочник</h2>
                <div class="mb-4">
                    <h3 class="text-sm font-semibold mb-2">Файлы:</h3>
                    <ul>
                        <li class="flex items-center gap-2 mb-2">
                            <span class="inline-block w-6 h-6 bg-gray-300 flex justify-center items-center rounded-md">
                                📄
                            </span>
                            &lt;!&ndash;                            <a :href="'/' + file" download v-for="file in parsedFiles()">&ndash;&gt;
                            &lt;!&ndash;                                Скачать&ndash;&gt;
                            &lt;!&ndash;                            </a>&ndash;&gt;
                        </li>
                    </ul>
                </div>
                <button class="flex items-center gap-2 text-blue-500">
                    <span class="inline-block w-6 h-6 bg-gray-300 flex justify-center items-center rounded-md">
                        ➕
                    </span>

                    <input @change="handleFileUpload" type="file" name="files[]" multiple>
                </button>
            </div>
            <div class="flex-1 p-6">
                <h2 class="text-lg font-bold mb-4">Общая информация:</h2>
                <div>
                    <label for="commentary" class="block mb-2 text-sm font-semibold">Комментарий</label>
                    <textarea v-model="form.commentary" id="commentary" rows="10"
                              class="w-full p-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Введите текст...">
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </textarea>.
                </div>
                <button @click="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Сохранить
                </button>
                <p class="mt-4 text-sm text-green-500 flex items-center gap-2">
                    ✅ Успешно сохранено. Справочник сохранен.
                </p>
            </div>
        </div>-->
    </AuthenticatedLayout>
</template>
