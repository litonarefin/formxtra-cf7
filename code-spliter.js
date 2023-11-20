const fs = require("fs")
const path = require("path")
const dotenv = require("dotenv")
dotenv.config()

const packageSlug = process.env.SLUG
if (!packageSlug)
{
    console.log("Please provide a package slug")
    process.exit(1)
}
const sourceFolderPath = "./built/" + packageSlug + "-pro"
const destinationFolderPath = "./built/" + packageSlug + "-free"

const functionNamesIfTrue = process.env.FUNCTION_NAME_IF_TRUE
    ? process.env.FUNCTION_NAME_IF_TRUE.split(",")
    : [null]
const functionNamesElseTrue = process.env.FUNCTION_NAME_ELSE_TRUE
    ? process.env.FUNCTION_NAME_ELSE_TRUE.split(",")
    : [null]

const jsCommentStartEnd = process.env.JS_COMMENT_START_END_PAIR
    ? process.env.JS_COMMENT_START_END_PAIR.split(",")
    : [null]

const cssCommentStartEnd = process.env.CSS_COMMENT_START_END_PAIR
    ? process.env.CSS_COMMENT_START_END_PAIR.split(",")
    : [null]

const writeFileRecursive = (file, data) =>
{
    const dirname = path.dirname(file)
    if (!fs.existsSync(dirname))
    {
        fs.mkdirSync(dirname, { recursive: true })
    }
    fs.writeFileSync(file, data)
}

function removeWhiteSpace(str)
{
    return str.replace(/\s/g, "")
}

function processPhpFileForIfTrue(
    sourceFilePath,
    destinationFilePath,
    functionName
)
{
    const content = fs.readFileSync(sourceFilePath, "utf8")

    const lines = content.split("\n")
    let ifBlock = ""
    let insideIfBlock = false
    let insideElseBlock = false
    let modifiedContent = ""

    for (const line of lines)
    {
        if (functionName === null) continue
        if (removeWhiteSpace(line).includes(removeWhiteSpace(functionName)))
        {
            console.log("function found: ", functionName)
            // modifiedContent += "//" + line + "\n"
            insideIfBlock = true
        } else
        {
            if (insideIfBlock)
            {
                if (removeWhiteSpace(line).includes(removeWhiteSpace("} else {")))
                {
                    console.log("else found")
                    insideElseBlock = true
                    insideIfBlock = false
                    // modifiedContent += "// " + line + "\n"
                } else
                {
                    console.log("keeping code: ", line)
                    ifBlock += line + "\n"
                    modifiedContent += ifBlock
                }
            } else if (insideElseBlock)
            {
                if (removeWhiteSpace(line).includes(removeWhiteSpace("}")))
                {
                    console.log("found end of else block")
                    insideElseBlock = false
                    // modifiedContent += "//" + line + "\n"
                    ifBlock = ""
                } else
                {
                    console.log("other codes")
                    // modifiedContent += "//" + line + "\n"
                }
            } else
            {
                // console.log("modifiedContent: ")
                modifiedContent += line + "\n"
            }
        }
    }
    writeFileRecursive(destinationFilePath, modifiedContent.trim())

    console.log("File written to: ", destinationFilePath)
}

function processPhpFileForElseTrue(
    sourceFilePath,
    destinationFilePath,
    functionName
)
{
    const content = fs.readFileSync(sourceFilePath, "utf8")

    const lines = content.split("\n")
    let codeToKeep = ""
    let insideIfBlock = false
    let insideElseBlock = false
    let modifiedContent = ""

    for (const line of lines)
    {
        if (functionName === null) continue
        if (removeWhiteSpace(line).includes(removeWhiteSpace(functionName)))
        {
            console.log("function found: ", functionName)
            // modifiedContent += "//" + line + "\n"
            insideIfBlock = true
        } else
        {
            if (insideIfBlock)
            {
                if (removeWhiteSpace(line).includes(removeWhiteSpace("} else {")))
                {
                    console.log("else found")
                    insideElseBlock = true
                    insideIfBlock = false
                    // modifiedContent += "//" + line + "\n"
                } else
                {
                    console.log("other codes")
                    // modifiedContent += "//" + line + "\n"
                }
            } else if (insideElseBlock)
            {
                if (removeWhiteSpace(line).includes(removeWhiteSpace("}")))
                {
                    console.log("found end of else block")
                    insideElseBlock = false
                    // modifiedContent += "//" + line + "\n"
                    codeToKeep = ""
                } else
                {
                    console.log("keeping code: ", line)
                    codeToKeep += line + "\n"
                    modifiedContent += codeToKeep
                }
            } else
            {
                modifiedContent += line + "\n"
            }
        }
    }
    writeFileRecursive(destinationFilePath, modifiedContent.trim())
    console.log("File written to: ", destinationFilePath)
}

function processFilesForComments(
    sourceFilePath,
    destinationFilePath,
    commentStart,
    commentEnd
)
{
    const content = fs.readFileSync(sourceFilePath, "utf8")

    const lines = content.split("\n")
    let codeToKeep = ""
    let insideComment = false
    let modifiedContent = ""

    for (const line of lines)
    {
        if (removeWhiteSpace(line).includes(removeWhiteSpace(commentStart)))
        {
            console.log("comment start: ", commentStart)
            //modifiedContent += "//" + line + "\n"
            insideComment = true
        } else
        {
            if (insideComment)
            {
                if (removeWhiteSpace(line).includes(removeWhiteSpace(commentEnd)))
                {
                    console.log("comment end")
                    insideComment = false
                    //modifiedContent += "//" + line + "\n"
                } else
                {
                    codeToKeep += "// for removal " + line + "\n"
                    //modifiedContent += codeToKeep
                }
            } else
            {
                modifiedContent += line + "\n"
            }
        }
    }
    writeFileRecursive(destinationFilePath, modifiedContent.trim())
}

function processFiles(sourceFolderPath, destinationFolderPath)
{
    const files = fs.readdirSync(sourceFolderPath)
    files.forEach((file) =>
    {
        const sourceFilePath = path.join(sourceFolderPath, file)
        const destinationFilePath = path.join(destinationFolderPath, file)
        const ext = path.extname(sourceFilePath)

        if (ext === ".php")
        {
            let alreadyCheckingForFunction = false

            for (const functionName of functionNamesIfTrue)
            {
                if (!alreadyCheckingForFunction)
                {
                    processPhpFileForIfTrue(
                        sourceFilePath,
                        destinationFilePath,
                        functionName
                    )
                    alreadyCheckingForFunction = true
                } else
                {
                    processPhpFileForIfTrue(
                        destinationFilePath,
                        destinationFilePath,
                        functionName
                    )
                }
            }

            for (const functionName of functionNamesElseTrue)
            {
                if (!alreadyCheckingForFunction)
                {
                    processPhpFileForElseTrue(
                        sourceFilePath,
                        destinationFilePath,
                        functionName
                    )
                    alreadyCheckingForFunction = true
                } else
                {
                    processPhpFileForElseTrue(
                        destinationFilePath,
                        destinationFilePath,
                        functionName
                    )
                }
            }
        } else if (ext === ".js")
        {
            let alreadyCheckingForFunction = false
            for (const comments of jsCommentStartEnd)
            {
                if (comments === null)
                {
                    const content = fs.readFileSync(sourceFilePath, "utf8")
                    writeFileRecursive(destinationFilePath, content)
                } else
                {
                    let jsComments = comments.split("-:-")
                    if (!alreadyCheckingForFunction)
                    {
                        processFilesForComments(
                            sourceFilePath,
                            destinationFilePath,
                            jsComments[0],
                            jsComments[1]
                        )
                        alreadyCheckingForFunction = true
                    } else
                    {
                        processFilesForComments(
                            destinationFilePath,
                            destinationFilePath,
                            jsComments[0],
                            jsComments[1]
                        )
                    }
                }
            }
        } else if (ext === ".css")
        {
            let alreadyCheckingForFunction = false
            for (const comments of cssCommentStartEnd)
            {
                if (comments === null)
                {
                    const content = fs.readFileSync(sourceFilePath, "utf8")
                    writeFileRecursive(destinationFilePath, content)
                } else
                {
                    let cssComments = comments.split("-:-")
                    if (!alreadyCheckingForFunction)
                    {
                        processFilesForComments(
                            sourceFilePath,
                            destinationFilePath,
                            cssComments[0],
                            cssComments[1]
                        )
                        alreadyCheckingForFunction = true
                    } else
                    {
                        processFilesForComments(
                            destinationFilePath,
                            destinationFilePath,
                            cssComments[0],
                            cssComments[1]
                        )
                    }
                }
            }
        } else
        {
            if (!fs.statSync(sourceFilePath).isDirectory())
            {
                const content = fs.readFileSync(sourceFilePath, "utf8")
                writeFileRecursive(destinationFilePath, content)
            }
        }

        if (fs.statSync(sourceFilePath).isDirectory())
        {
            const subfolderName = path.basename(sourceFilePath)
            const destinationSubfolderPath = path.join(
                destinationFolderPath,
                subfolderName
            )
            fs.mkdirSync(destinationSubfolderPath, { recursive: true })
            processFiles(sourceFilePath, destinationSubfolderPath)
        }
    })
}

processFiles(sourceFolderPath, destinationFolderPath)
