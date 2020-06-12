<template>
  <div>
    <el-upload
      class="avatar-uploader"
      action="http://this.de/upload"
      :auto-upload="false"
      :show-file-list="false"
      :on-change="fileAdded"
      :before-upload="beforeUpload">
      <div v-if="imageUrl" class="upload-container">
        <i v-if="imageUrl" class="uploader-icon-delete el-icon-delete" @click.stop="remove"/>
        <img v-if="imageUrl" :src="imageUrl" class="avatar">
      </div>
      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
  </div>
</template>

<script>
import api from '../../api'

export default {
  name: 'GenericUpload',
  props: ['path', 'docs'],
  data() {
    return {
      imageUrl: '',
    }
  },
  watch: {
    docs() {
      const docs = this.docs.filter(doc => doc.path == this.path)
      if (docs.length > 0) {
        this.imageUrl = docs[0].url
      } else {
        this.imageUrl = ''
      }
    }
  },
  methods: {
      fileAdded(file) {
        if (typeof file == 'array') file = file[0]
        console.log('Added', this.path, file)
        this.imageUrl = URL.createObjectURL(file.raw);
        this.$emit('docs-added', {path: this.path, files:[file.raw]})
      },
      beforeUpload() {
        
      },
      remove() {
        this.imageUrl = ''
        this.$emit('docs-removed', {path: this.path, files:this.docs.filter(doc => doc.path == this.path)})
      }
  }
}
</script>

<style lang="scss">
  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  i.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
    object-fit: cover
  }
  .upload-container {
    position: relative;
  }
  .uploader-icon-delete {
    position: absolute;
    bottom: 10px;
    right: 10px;
  }
</style>