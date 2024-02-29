#
#   cd project1
#   cat source.IPPcode24 | python3.10 parse.py
# 
import sys
import re
import xml.etree.ElementTree as ET # alias
import xml.dom.minidom

def arg_help():
    print("This is a simple help message.")
    print("Usage: python script.py [options]")
    print("-h, --help: Display this help message")
    sys.exit(0)
    
class Unk:
    pass 

class Symb:
    pass

class Type:
    pass

class Var(Symb):
    pass

class Label:
    pass

class LVTS(Label, Var, Type, Symb):
    pass

# class EOL:
#     pass


class Token:
    def __init__(self, data, type):
        self.data = data
        self.type = type
    def printAtr(self):
        return f"Token: {'/n'} Type: {self.type}" if self.data == "\n" else f"Token: {self.data} Type: {self.type}"

def prettify_xml(xml_string):
    ugly_xml = xml.dom.minidom.parseString(xml_string)
    return ugly_xml.toprettyxml(encoding="UTF-8")

def VarSymbLabel(type):
    if type == "var":
        return Var
    elif type == "string" or type == "bool" or type == "int" or type == "nil":
        return Symb
    elif type == "type":
        return Type
    elif type == "label":
        return Label
    elif type == "unknown":
        return Unk
    # elif type == "EOL":
    #     return EOL
def printVarSymbLabel(type):
    if type == Symb:
        return "Symb"
    elif type == Var:
        return "Var"
    elif type == Label:
        return "Label"
    elif type == Type:
        return "Type"
    elif type == Unk:
        return "Unk"
    # elif type == EOL:
    #     return "EOL"

INSTRUCTIONS: dict[str, list[LVTS]] = {
    "MOVE": [Var, Symb],
    "CREATEFRAME": [],
    "PUSHFRAME": [],
    "POPFRAME": [],
    "DEFVAR": [Var],
    "CALL": [Label],
    "RETURN": [],
    "PUSHS": [Symb],
    "POPS": [Var],
    "ADD": [Var, Symb, Symb],
    "SUB": [Var, Symb, Symb],
    "MUL": [Var, Symb, Symb],
    "IDIV": [Var, Symb, Symb],
    "LT": [Var, Symb, Symb],
    "GT": [Var, Symb, Symb],
    "EQ": [Var, Symb, Symb],
    "AND": [Var, Symb, Symb],
    "OR": [Var, Symb, Symb],
    "NOT": [Var, Symb],
    "INT2CHAR": [Var, Symb],
    "STRI2INT": [Var, Symb, Symb],
    "READ": [Var, Type], # zmen symb na type
    "WRITE": [Symb],
    "CONCAT": [Var, Symb, Symb],
    "STRLEN": [Var, Symb],
    "GETCHAR": [Var, Symb, Symb],
    "SETCHAR": [Var, Symb, Symb],
    "TYPE": [Var, Symb],
    "LABEL": [Label],
    "JUMP": [Label],
    "JUMPIFEQ": [Label, Symb, Symb],
    "JUMPIFNEQ": [Label, Symb, Symb],
    "EXIT": [Symb],
    "DPRINT": [Symb],
    "BREAK": [],
    # "EOL": []
}

def main():
    array = []


    if "--help" in sys.argv: # pozri predavanie arguemntov
        arg_help()
        sys.exit(0)
  
    file = sys.stdin.read()

    # Remove comments from input text
    cleaned_text = re.sub(r"#.*?$", '', file, flags=re.MULTILINE)

    # Extract tokens from cleaned text
    tokens = re.findall(r"\S+", cleaned_text) # pridaj eol na to aby si zistil ci je len jedna instrukcia na riadok


    for token_data in tokens:
        token = Token(token_data, "")  # Create a Token instance for each token
        set_type(token)  # Set the type for the token
        array.append(token)
    # array.append(Token("\n", "EOL"))

    # for token in array:
    #     print(token.printAtr())
    output = parser(array)
    # print("return shit : ", output)
    sys.exit(output)


def set_type(token):
    if re.match(r"(?:bool@(true|false))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "bool"
    elif re.match(r"(?:string@((?:\\[0-9]{3}|[^\n\t #]|)+))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "string"
    elif re.match(r"(?:(nil@(nil)))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "nil"
    elif re.match(r"(?:int@([+-]?[0-9]+))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "int"
    elif re.match(r"GF@(?=[a-zA-Z_\-$&%*!?])[0-9a-zA-Z_\-$&%*!?]+", token.data): #ZACINAJICI PISMENEM NEBO ZNAKEM
        token.type = "var"
    elif re.match(r"LF@(?=[a-zA-Z_\-$&%*!?])[0-9a-zA-Z_\-$&%*!?]+", token.data):
        token.type = "var"
    elif re.match(r"TF@(?=[a-zA-Z_\-$&%*!?])[0-9a-zA-Z_\-$&%*!?]+", token.data):
        token.type = "var"
    elif token.data.upper() == "MOVE":  
        token.type = "MOVE"
    elif token.data.upper() == "CREATEFRAME":  
        token.type = "CREATEFRAME"
    elif token.data.upper() == "PUSHFRAME":
        token.type = "PUSHFRAME"
    elif token.data.upper() == "POPFRAME":
        token.type = "POPFRAME"
    elif token.data.upper() == "DEFVAR":
        token.type = "DEFVAR"
    elif token.data.upper() == "CALL":
        token.type = "CALL"
    elif token.data.upper() == "RETURN":
        token.type = "RETURN"
    elif token.data.upper() == "PUSHS":
        token.type = "PUSHS"
    elif token.data.upper() == "POPS":
        token.type = "POPS"
    elif token.data.upper() == "ADD":
        token.type = "ADD"
    elif token.data.upper() == "SUB":
        token.type = "SUB"
    elif token.data.upper() == "MUL":
        token.type = "MUL"
    elif token.data.upper() == "IDIV":
        token.type = "IDIV"
    elif token.data.upper() == "LT":
        token.type = "LT"
    elif token.data.upper() == "GT":
        token.type = "GT"
    elif token.data.upper() == "EQ":
        token.type = "EQ"
    elif token.data.upper() == "AND":
        token.type = "AND"
    elif token.data.upper() == "OR":
        token.type = "OR"
    elif token.data.upper() == "NOT":
        token.type = "NOT"
    elif token.data.upper() == "INT2CHAR":
        token.type = "INT2CHAR"
    elif token.data.upper() == "STRI2INT":
        token.type = "STRI2INT"
    elif token.data.upper() == "READ":
        token.type = "READ"
    elif token.data.upper() == "WRITE":
        token.type = "WRITE"
    elif token.data.upper() == "CONCAT":
        token.type = "CONCAT"
    elif token.data.upper() == "STRLEN":
        token.type = "STRLEN"
    elif token.data.upper() == "GETCHAR":
        token.type = "GETCHAR"
    elif token.data.upper() == "SETCHAR":
        token.type = "SETCHAR"
    elif token.data.upper() == "TYPE":
        token.type = "TYPE"
    elif token.data.upper() == "LABEL":
        token.type = "LABEL"
    elif token.data.upper() == "JUMP":
        token.type = "JUMP"
    elif token.data.upper() == "JUMPIFEQ":
        token.type = "JUMPIFEQ"
    elif token.data.upper() == "JUMPIFNEQ":
        token.type = "JUMPIFNEQ"
    elif token.data.upper() == "EXIT":
        token.type = "EXIT"
    elif token.data.upper() == "DPRINT":
        token.type = "DPRINT"
    elif token.data.upper() == "BREAK":
        token.type = "BREAK"
    elif token.data == ".IPPcode24":
        token.type = "header"
    elif token.data == "int":
        token.type = "type"
    elif token.data == "string":
        token.type = "type"
    elif token.data == "bool":
        token.type = "type"
    elif re.match(r"(?:[a-zA-Z0-9_\-$&%*!?])", token.data):
        token.type = "label"
    # elif token.data == "\n":
    #     token.type = "EOL"
    else:
        token.type = "unknown"

    return True

def parser(token_array):
    # print("\n------------PARSER------------\n")
    root = ET.Element("program")
    root.set("language", "IPPcode24")

    i = 0
    j = 0
    order = 1
    if token_array[0].type != "header" :
        return 21
    # elif token_array[1].type == "unknown":
    #     return 22
    
    token_array.pop(0)
    # token_array.pop(0) # remove the EOL token

    while i < len(token_array):
        if token_array[i].type not in INSTRUCTIONS:
            print("array position ")
            print(i)
            # print("prev instructions " + token_array[i-1].type)
            print("not in instructions " + token_array[i].type)
            return 23
        excepted_tokens = INSTRUCTIONS[token_array[i].type]
        # vytvoris elemment pre danu instrukciu
        instruction = ET.SubElement(root, "instruction")
        instruction.set("order", str(order))
        instruction.set("opcode", token_array[i].type)
        i += 1 # proc to tu je dpc ????
        while j < len(excepted_tokens):
            if i >= len(token_array): # malo argumentov
                return 23
            arg = ET.SubElement(instruction, "arg" + str(j+1))
            arg.set("type", token_array[i].type)
            arg.text = token_array[i].data
            # budes udavat argumenty danej instrukcii
            # if type(VarSymbLabel(token_array[i].type)) !=  type(excepted_tokens[j]):
            if (VarSymbLabel(token_array[i].type) != excepted_tokens[j]) and not(issubclass(VarSymbLabel(token_array[i].type), excepted_tokens[j])):
                print("expected len " + str(len(excepted_tokens)))
                print("array position ")
                print(i)
                print(j)
                # print("prev instructions " + token_array[i-2].type)
                # print("prev instructions " + token_array[i-1].type)
                print("token in array : " + token_array[i].type + " : " + printVarSymbLabel(VarSymbLabel(token_array[i].type)))
                print("expected token : " + printVarSymbLabel(excepted_tokens[j]))
                print("Type returned by VarSymbLabel:", VarSymbLabel(token_array[i].type))
                print("Expected token type:", excepted_tokens[j])
                print("Type returned by issubclass:", issubclass(VarSymbLabel(token_array[i].type), excepted_tokens[j]))
                return 23
            # print("token in array : " + token_array[i].type + " : " + printVarSymbLabel(VarSymbLabel(token_array[i].type)))
            # print("expected token : " + printVarSymbLabel(excepted_tokens[j]))
            i += 1
            j += 1
            # if j > len(excepted_tokens) and token_array[i].type != "\n":
            #     return 23
        j = 0
        order += 1
    xml_str = ET.tostring(root, encoding='utf-8')
    prettified_xml = prettify_xml(xml_str)
    with open("output.xml", "wb") as output_file:  # Use binary mode for writing
        output_file.write(prettified_xml)
    good = prettified_xml.decode("utf-8")
    print(good, file=sys.stdout)
    return 0    
main()

