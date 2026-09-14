/* filepond-plugin-image-caption.js */
import { FileStatus } from 'filepond'

/**
 * FilePond image caption plugin
 *
 * FilePondPluginImageCaption 1.0.3
 * Author: clementmas
 * Licensed under MIT, https://opensource.org/licenses/MIT
 */
export default function ({ addFilter, utils }) {
    const { Type, createRoute, createView } = utils

    // Called when a new file is added
    addFilter('CREATE_VIEW', function (viewAPI) {
        const { is, view, query } = viewAPI

        // Make sure the option `addImageCaption` is enabled
        if (!query('GET_ADD_IMAGE_CAPTION')) return

        // Skip invalid file types
        if (!is('file')) return

        function onItemAdded({ root, props: { id } }) {
            const item = query('GET_ITEM', id)

            // Item could theoretically have been removed in the mean time
            if (!item || item.archived) return

            const value = item.getMetadata('caption')
            const uuid = item.getMetadata('uuid')

            const isInvalid = item.status === FileStatus.LOAD_ERROR

            // Append image caption input
            root.ref.imageCaption = view.appendChildView(
                view.createChildView(
                    createView(addCaptionInputField(value, isInvalid, uuid, id)),
                    {
                        id,
                    },
                ),
            )

            // Disable file action buttons tabindex (cancel, revert, etc.)
            // to easily tab from one caption input to another
            view.element
                .querySelectorAll('button')
                .forEach((button) => button.setAttribute('tabindex', -1))
        }

        view.registerWriter(
            createRoute({
                DID_INIT_ITEM: onItemAdded,
            }),
        )
    })

    // Plugin config options
    return {
        options: {
            // Enable or disable image captions
            addImageCaption: [true, Type.BOOLEAN],

            // Input placeholder
            imageCaptionPlaceholder: [null, Type.STRING],

            // Input max length
            imageCaptionMaxLength: [null, Type.INT],
        },
    }
}

// Create DOM input
function addCaptionInputField(value, isInvalid, uuid, id) {
    return {
        name: 'image-caption-input',
        tag: 'input',
        ignoreRect: true,
        create: function create({ root }) {
            // Ensure type is text
            root.element.setAttribute('type', 'text')

            // Placeholder
            const placeholder = root.query('GET_IMAGE_CAPTION_PLACEHOLDER')
            if (placeholder) {
                root.element.setAttribute('placeholder', placeholder)
            }

            // Max length
            const maxLength = root.query('GET_IMAGE_CAPTION_MAX_LENGTH')
            if (maxLength) {
                root.element.setAttribute('maxlength', maxLength)
            }

            // Autocomplete off
            root.element.setAttribute('autocomplete', 'off')

            // Visually hide the element if the file is invalid but keep the input
            // to make sure the "captions[]" index will stay in sync with the FilePond photos
            if (isInvalid) {
                root.element.classList.add('image-caption-input-invalid')
            }

            // Prevent Enter key from submitting form
            root.element.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault()
                }
            })

            // Store internal item id and file key on input element
            if (id) {
                root.element.dataset.filepondId = id
            }

            if (uuid) {
                root.element.dataset.fileKey = uuid
            }

            // Input is always enabled so user can write captions immediately

            // Dispatch custom event on input for Livewire integration
            // The parent Alpine component listens for this and syncs via $wire.set()
            root.element.addEventListener('input', function (e) {
                const fileKey = root.element.dataset.fileKey
                if (fileKey) {
                    root.element.dispatchEvent(
                        new CustomEvent('caption-change', {
                            bubbles: true,
                            detail: {
                                fileKey,
                                value: e.target.value,
                            },
                        }),
                    )
                }
            })

            // Value
            if (value) {
                root.element.value = value
            }
        },
    }
}
