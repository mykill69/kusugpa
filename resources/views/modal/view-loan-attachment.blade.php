<!-- Attachments Viewer Modal -->
<div x-show="showAttachmentsModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @keydown.escape="showAttachmentsModal = false">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" @click="showAttachmentsModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-paperclip text-gray-400"></i>
                    Attachments
                </h3>
                <button @click="showAttachmentsModal = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Loan Info -->
            <div class="bg-gray-50 rounded-xl p-3 mb-4" x-show="viewingLoan">
                <p class="text-sm font-semibold text-gray-900" x-text="viewingLoan?.loan_number"></p>
                <p class="text-xs text-gray-500" x-text="viewingLoan?.planter_name + ' (' + viewingLoan?.planter_code + ')'"></p>
            </div>

            <!-- Attachments List -->
            <div class="space-y-2" id="attachmentsList">
                <div x-show="loadingAttachments" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Loading attachments...</p>
                </div>
                <div x-show="!loadingAttachments && attachments.length === 0" class="text-center py-8">
                    <i class="fas fa-paperclip text-4xl text-gray-200 mb-2 block"></i>
                    <p class="text-sm text-gray-500">No attachments uploaded</p>
                </div>
                <template x-for="att in attachments" :key="att.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                :class="getFileBgColor(att.mime_type)">
                                <i :class="getFileIcon(att.mime_type) + ' text-sm'"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="att.original_filename"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500" x-text="att.file_size_formatted"></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-200 text-gray-600" x-text="att.document_type"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a :href="att.view_url" target="_blank" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="View">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a :href="att.download_url" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                title="Download">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Upload New Attachment (if user can process loans) -->
            @if(in_array(auth()->user()->role, ['Administrator', 'super_admin', 'manager', 'loan_officer']))
            <div class="border-t border-gray-100 pt-4 mt-4">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Upload New Attachment</h4>
                <form id="uploadAttachmentForm" @submit.prevent="uploadNewAttachment()" class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <select id="newDocType" class="border border-gray-200 rounded-xl px-3 py-2 text-xs">
                            <option value="proof">Proof of Identity</option>
                            <option value="agreement">Signed Agreement</option>
                            <option value="id">Valid ID</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="text" id="newDocDesc" placeholder="Description" class="border border-gray-200 rounded-xl px-3 py-2 text-xs">
                    </div>
                    <div class="flex gap-2">
                        <input type="file" id="newAttachmentFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700">
                        <button type="submit" :disabled="uploadingAttachment"
                            class="px-4 py-2 bg-primary-600 text-white rounded-xl text-xs font-semibold hover:bg-primary-700 disabled:opacity-50">
                            <span x-show="!uploadingAttachment"><i class="fas fa-upload mr-1"></i> Upload</span>
                            <span x-show="uploadingAttachment"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>